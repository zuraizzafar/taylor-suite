<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Setting;
use App\Traits\HasBranchScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class OrderController extends Controller
{
    use HasBranchScope;

    public function index(Request $request): View
    {
        $query = Order::with(['customer', 'branch'])->withCount('suits');

        $this->branchQuery($query);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn($cq) =>
                    $cq->where('name', 'like', "%{$search}%")
                       ->orWhere('mobile', 'like', "%{$search}%")
                       ->orWhere('file_number', 'like', "%{$search}%")
                )->orWhere('order_number', 'like', "%{$search}%");
            });
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('orders.index', compact('orders', 'search'));
    }

    public function create(Request $request): View
    {
        // Branch managers only see their own branch's customers
        $customers = Customer::orderBy('name');
        $this->branchQuery($customers);
        $customers = $customers->get();

        $selectedCustomer = $request->input('customer_id')
            ? Customer::find($request->input('customer_id'))
            : null;

        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        return view('orders.create', compact('customers', 'selectedCustomer', 'branches'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id'    => ['required', 'exists:customers,id'],
            'branch_id'      => ['nullable', 'exists:branches,id'],
            'order_date'     => ['required', 'date'],
            'delivery_date'  => ['required', 'date', 'after_or_equal:order_date'],
            'total_amount'   => ['required', 'numeric', 'min:0'],
            'advance_amount' => ['required', 'numeric', 'min:0'],
            'notes'          => ['nullable', 'string'],
        ]);

        $advanceAmount = (float) $data['advance_amount'];
        unset($data['advance_amount']); // managed via Payment record below

        $data['order_number']   = Order::nextOrderNumber();
        $data['advance_amount'] = 0;
        $data['balance_amount'] = $data['total_amount'];

        if (empty($data['branch_id']) && $branchId = $this->currentBranchId()) {
            $data['branch_id'] = $branchId;
        }

        $order = Order::create($data);

        // Create initial advance as a Payment so recalculateBalance() tracks it correctly
        if ($advanceAmount > 0) {
            Payment::create([
                'order_id'     => $order->id,
                'branch_id'    => $order->branch_id,
                'received_by'  => auth()->id(),
                'amount'       => $advanceAmount,
                'method'       => 'cash',
                'payment_date' => $order->order_date,
                'reference'    => 'INITIAL_ADVANCE',
                'note'         => 'Initial advance',
            ]);
        }
        $order->recalculateBalance();

        return redirect()->route('orders.show', $order)
            ->with('success', "Order {$order->order_number} created.");
    }

    public function show(Order $order): View
    {
        $order->load(['customer', 'branch', 'suits.worker', 'suits.measurement', 'payments.receivedBy']);
        return view('orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        $customers      = Customer::orderBy('name')->get();
        $branches       = Branch::where('is_active', true)->orderBy('name')->get();
        $initialAdvance = (float) $order->payments()->where('reference', 'INITIAL_ADVANCE')->sum('amount');
        return view('orders.edit', compact('order', 'customers', 'branches', 'initialAdvance'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'branch_id'      => ['nullable', 'exists:branches,id'],
            'order_date'     => ['required', 'date'],
            'delivery_date'  => ['required', 'date', 'after_or_equal:order_date'],
            'total_amount'   => ['required', 'numeric', 'min:0'],
            'advance_amount' => ['required', 'numeric', 'min:0'],
            'notes'          => ['nullable', 'string'],
        ]);

        $newAdvance = (float) $data['advance_amount'];
        unset($data['advance_amount'], $data['balance_amount']); // computed by recalculateBalance

        $order->update($data);

        // Sync the INITIAL_ADVANCE payment record
        $advancePayment = $order->payments()->where('reference', 'INITIAL_ADVANCE')->first();
        if ($newAdvance > 0) {
            if ($advancePayment) {
                $advancePayment->update(['amount' => $newAdvance]);
            } else {
                Payment::create([
                    'order_id'     => $order->id,
                    'branch_id'    => $order->branch_id,
                    'received_by'  => auth()->id(),
                    'amount'       => $newAdvance,
                    'method'       => 'cash',
                    'payment_date' => $order->order_date,
                    'reference'    => 'INITIAL_ADVANCE',
                    'note'         => 'Initial advance',
                ]);
            }
        } else {
            $advancePayment?->delete();
        }

        $order->recalculateBalance();

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }

    public function invoice(Order $order): Response
    {
        $order->load(['customer', 'suits.worker', 'suits.measurement']);
        $settings        = Setting::allKeyed();
        $previousBalance = $order->customer->outstandingBalance($order->id);

        $pdf = Pdf::loadView('orders.invoice-pdf', compact('order', 'settings', 'previousBalance'))
            ->setPaper('a4', 'portrait');

        $filename = "invoice-{$order->order_number}.pdf";

        return env('PDF_MODE', 'download') === 'stream'
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }
}
