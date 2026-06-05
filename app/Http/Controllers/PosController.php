<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Measurement;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Models\StitchType;
use App\Models\Suit;
use App\Models\SuitType;
use App\Models\Worker;
use App\Traits\HasBranchScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class PosController extends Controller
{
    use HasBranchScope;

    /**
     * POS screen: all-in-one order creation
     */
    public function index(Request $request): View
    {
        $workers = Worker::query()->where('is_active', true)
            ->when($this->currentBranchId(), fn($q, $b) => $q->where('branch_id', $b))
            ->orderBy('name')->get();

        $stitchTypes = StitchType::query()->where('is_active', true)->orderBy('name')->get();
        $suitTypes   = SuitType::where('is_active', true)->orderBy('name')->pluck('name');
        $branches    = Branch::query()->where('is_active', true)->orderBy('name')->get();

        // Pre-select customer if passed via URL (e.g. coming from customer page)
        $preCustomer = $request->input('customer_id')
            ? Customer::with('measurements')->find($request->input('customer_id'))
            : null;

        return view('pos.index', compact('workers', 'stitchTypes', 'suitTypes', 'branches', 'preCustomer'));
    }

    /**
     * AJAX: search customers
     * GET /pos/customers/search?q=...
     */
    public function searchCustomers(Request $request): JsonResponse
    {
        $q = $request->input('q', '');

        $query = Customer::with('measurements')
            ->where(function ($cq) use ($q) {
                $cq->where('name', 'like', "%{$q}%")
                   ->orWhere('mobile', 'like', "%{$q}%")
                   ->orWhere('file_number', 'like', "%{$q}%");
            });

        $this->branchQuery($query);

        $customers = $query->limit(10)->get()->map(fn($c) => [
            'id'          => $c->id,
            'name'        => $c->name,
            'mobile'      => $c->mobile,
            'file_number' => $c->file_number,
            'address'     => $c->address,
            'measurement' => $c->measurements->first(),
        ]);

        return response()->json($customers);
    }

    /**
     * Store the complete POS transaction:
     * 1) Create or find customer
     * 2) Save/update measurements
     * 3) Create order
     * 4) Create each suit line
     * 5) Record advance payment
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            // Customer
            'customer_id'        => ['nullable', 'exists:customers,id'],
            'customer_name'      => ['required_without:customer_id', 'string', 'max:255'],
            'customer_mobile'    => ['required_without:customer_id', 'string', 'max:20'],
            'customer_address'   => ['nullable', 'string', 'max:500'],
            'branch_id'          => ['nullable', 'exists:branches,id'],
            // Order
            'order_date'         => ['required', 'date'],
            'delivery_date'      => ['required', 'date', 'after_or_equal:order_date'],
            'total_amount'       => ['required', 'numeric', 'min:0'],
            'advance_amount'     => ['required', 'numeric', 'min:0'],
            'order_notes'        => ['nullable', 'string'],
            // Suits — array
            'suits'              => ['required', 'array', 'min:1'],
            'suits.*.suit_type'  => ['required', 'string', 'max:100'],
            'suits.*.fabric_meter' => ['required', 'numeric', 'min:0'],
            'suits.*.stitch_type_id' => ['nullable', 'exists:stitch_types,id'],
            'suits.*.worker_id'  => ['nullable', 'exists:workers,id'],
            'suits.*.notes'      => ['nullable', 'string'],
        ]);

        $total   = (float) $request->input('total_amount');
        $advance = (float) $request->input('advance_amount');

        if ($advance > $total) {
            return back()
            ->withErrors(['advance_amount' => 'Advance cannot be greater than the total amount.'])
                ->withInput();
        }

        DB::transaction(function () use ($request) {
            $branchId = $request->input('branch_id') ?? $this->currentBranchId();

            // ── 1. Customer ────────────────────────────────────────────────────
            if ($request->filled('customer_id')) {
                $customer = Customer::findOrFail($request->input('customer_id'));
            } else {
                $fileData = Customer::nextFileNumber();
                $customer = Customer::create([
                    'file_sequence' => $fileData['file_sequence'],
                    'file_number'   => $fileData['file_number'],
                    'name'          => $request->input('customer_name'),
                    'mobile'        => $request->input('customer_mobile'),
                    'address'       => $request->input('customer_address'),
                    'branch_id'     => $branchId,
                ]);
            }

            // ── 2. Measurements (optional) ─────────────────────────────────────
            $measurementId = null;
            $mData = array_filter($request->input('measurement', []), fn($v) => $v !== null && $v !== '');
            if (!empty($mData)) {
                $measurement = Measurement::updateOrCreate(
                    ['customer_id' => $customer->id],
                    array_merge($mData, ['customer_id' => $customer->id])
                );
                $measurementId = $measurement->id;
            }

            // ── 3. Order ───────────────────────────────────────────────────────
            $total   = (float) $request->input('total_amount');
            $advance = (float) $request->input('advance_amount');

            $order = Order::create([
                'customer_id'    => $customer->id,
                'branch_id'      => $branchId,
                'order_number'   => Order::nextOrderNumber(),
                'order_date'     => $request->input('order_date'),
                'delivery_date'  => $request->input('delivery_date'),
                'total_amount'   => $total,
                'advance_amount' => 0,
                'balance_amount' => $total,
                'notes'          => $request->input('order_notes'),
                'extras'         => $this->parsePosExtras($request),
            ]);

            if ($advance > 0) {
                Payment::create([
                    'order_id'     => $order->id,
                    'branch_id'    => $branchId,
                    'received_by'  => $request->user()?->id,
                    'amount'       => $advance,
                    'method'       => $request->input('payment_method', 'cash'),
                    'payment_date' => $request->input('order_date'),
                    'reference'    => 'INITIAL_ADVANCE',
                    'note'         => 'Advance — POS',
                ]);
                $order->recalculateBalance();
            }

            // ── 4. Suits ───────────────────────────────────────────────────────
            $this->_order = $order; // store for redirect
            foreach ($request->input('suits') as $suitData) {
                $worker     = isset($suitData['worker_id']) ? Worker::query()->find($suitData['worker_id']) : null;
                $stitchType = isset($suitData['stitch_type_id']) ? StitchType::query()->find($suitData['stitch_type_id']) : null;

                $earning = null;
                if ($stitchType) {
                    $earning = $stitchType->priceForWorker($worker);
                } elseif ($worker?->rate_per_suit) {
                    $earning = (float) $worker->rate_per_suit;
                }

                $lastSuitNum = Suit::max('suit_number') ?? 0;
                $suitNumber  = $lastSuitNum + 1;
                $suitCode    = 'S' . str_pad($suitNumber, 5, '0', STR_PAD_LEFT);

                Suit::create([
                    'customer_id'        => $customer->id,
                    'order_id'           => $order->id,
                    'measurement_id'     => $measurementId,
                    'worker_id'          => $suitData['worker_id']      ?? null,
                    'stitch_type_id'     => $suitData['stitch_type_id'] ?? null,
                    'branch_id'          => $branchId,
                    'suit_number'        => $suitNumber,
                    'suit_code'          => $suitCode,
                    'suit_type'          => $suitData['suit_type'],
                    'fabric_meter'       => $suitData['fabric_meter'],
                    'fabric_description' => $suitData['fabric_description'] ?? null,
                    'notes'              => $suitData['notes']              ?? null,
                    'status'             => 'pending',
                    'worker_earning'     => $earning,
                ]);
            }
        });

        $order = $this->_order ?? null;

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order created successfully from POS.');
    }

    private $_order;

    private function parsePosExtras(Request $request): array
    {
        $names  = $request->input('extra_name', []);
        $prices = $request->input('extra_price', []);
        $extras = [];
        foreach ($names as $i => $name) {
            $name  = trim($name);
            $price = (float) ($prices[$i] ?? 0);
            if ($name !== '') {
                $extras[] = ['name' => $name, 'price' => $price];
            }
        }
        return $extras;
    }
}
