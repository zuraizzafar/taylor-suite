<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Quotation;
use App\Models\Setting;
use App\Services\TaxService;
use App\Traits\HasBranchScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class QuotationController extends Controller
{
    use HasBranchScope;

    public function index(Request $request): View
    {
        $query = Quotation::with(['customer', 'branch'])->withCount('items');

        $this->branchQuery($query);

        if ($search = $request->input('search')) {
            $query->where(function ($q) use ($search) {
                $q->whereHas('customer', fn ($cq) =>
                    $cq->where('name', 'like', "%{$search}%")
                       ->orWhere('mobile', 'like', "%{$search}%")
                       ->orWhere('file_number', 'like', "%{$search}%")
                )->orWhere('quotation_number', 'like', "%{$search}%");
            });
        }

        $quotations = $query->latest()->paginate(20)->withQueryString();

        return view('quotations.index', compact('quotations', 'search'));
    }

    public function create(Request $request): View
    {
        $customers = Customer::orderBy('name');
        $this->branchQuery($customers);
        $customers = $customers->get();

        $selectedCustomer = $request->input('customer_id')
            ? Customer::find($request->input('customer_id'))
            : null;

        $branches = Branch::where('is_active', true)->orderBy('name')->get();
        $taxInit  = TaxService::formInit('quotation', $this->currentBranchId());

        return view('quotations.create', compact('customers', 'selectedCustomer', 'branches', 'taxInit'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id'         => ['required', 'exists:customers,id'],
            'branch_id'           => ['nullable', 'exists:branches,id'],
            'quotation_date'      => ['required', 'date'],
            'validity_days'       => ['required', 'integer', 'min:1'],
            'advance_percentage'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'design_reference'    => ['nullable', 'string'],
            'delivery_note'       => ['nullable', 'string', 'max:500'],
            'discount_type'       => ['nullable', 'in:percent,fixed'],
            'discount_value'      => ['nullable', 'numeric', 'min:0'],
            'tax_mode'            => ['nullable', 'in:none,exclusive,inclusive'],
            'tax_rate'            => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sample_image_1'      => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'sample_image_2'      => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'notes'               => ['nullable', 'string'],
            'description'         => ['required', 'array', 'min:1'],
            'description.*'       => ['nullable', 'string'],
            'qty'                 => ['required', 'array'],
            'qty.*'               => ['nullable', 'numeric', 'min:0'],
            'rate'                => ['required', 'array'],
            'rate.*'              => ['nullable', 'numeric', 'min:0'],
        ]);

        if (empty($data['branch_id']) && $branchId = $this->currentBranchId()) {
            $data['branch_id'] = $branchId;
        }

        $quotation = Quotation::create([
            'customer_id'        => $data['customer_id'],
            'branch_id'          => $data['branch_id'] ?? null,
            'quotation_number'   => Quotation::nextQuotationNumber(),
            'quotation_date'     => $data['quotation_date'],
            'validity_days'      => $data['validity_days'],
            'advance_percentage' => $data['advance_percentage'] ?? 50,
            'design_reference'   => $data['design_reference'] ?? null,
            'delivery_note'      => $data['delivery_note'] ?? null,
            'notes'              => $data['notes'] ?? null,
            'status'             => 'draft',
        ] + $this->taxFields($request, $data['branch_id'] ?? null));

        $this->syncItems($quotation, $request);
        $this->syncSampleImages($quotation, $request);
        $quotation->recalculateTotals();

        return redirect()->route('quotations.show', $quotation)
            ->with('success', "Quotation {$quotation->quotation_number} created.");
    }

    public function show(Quotation $quotation): View
    {
        $quotation->load(['customer', 'branch', 'items', 'convertedOrder']);
        $tax = TaxService::resolve('quotation', $quotation->branch_id);
        return view('quotations.show', compact('quotation', 'tax'));
    }

    public function edit(Quotation $quotation): View
    {
        $quotation->load('items');
        $customers = Customer::orderBy('name')->get();
        $branches  = Branch::where('is_active', true)->orderBy('name')->get();

        $taxInit = TaxService::formInit('quotation', $quotation->branch_id, $quotation);

        return view('quotations.edit', compact('quotation', 'customers', 'branches', 'taxInit'));
    }

    public function update(Request $request, Quotation $quotation): RedirectResponse
    {
        $data = $request->validate([
            'branch_id'           => ['nullable', 'exists:branches,id'],
            'quotation_date'      => ['required', 'date'],
            'validity_days'       => ['required', 'integer', 'min:1'],
            'advance_percentage'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'design_reference'    => ['nullable', 'string'],
            'delivery_note'       => ['nullable', 'string', 'max:500'],
            'discount_type'       => ['nullable', 'in:percent,fixed'],
            'discount_value'      => ['nullable', 'numeric', 'min:0'],
            'tax_mode'            => ['nullable', 'in:none,exclusive,inclusive'],
            'tax_rate'            => ['nullable', 'numeric', 'min:0', 'max:100'],
            'sample_image_1'      => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'sample_image_2'      => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp', 'max:4096'],
            'notes'               => ['nullable', 'string'],
            'description'         => ['required', 'array', 'min:1'],
            'description.*'       => ['nullable', 'string'],
            'qty'                 => ['required', 'array'],
            'qty.*'               => ['nullable', 'numeric', 'min:0'],
            'rate'                => ['required', 'array'],
            'rate.*'              => ['nullable', 'numeric', 'min:0'],
        ]);

        $branchId = array_key_exists('branch_id', $data) ? $data['branch_id'] : $quotation->branch_id;

        $quotation->update($this->taxFields($request, $branchId) + [
            'branch_id'          => $branchId,
            'quotation_date'     => $data['quotation_date'],
            'validity_days'      => $data['validity_days'],
            'advance_percentage' => $data['advance_percentage'] ?? 50,
            'design_reference'   => $data['design_reference'] ?? null,
            'delivery_note'      => $data['delivery_note'] ?? null,
            'notes'              => $data['notes'] ?? null,
        ]);

        $this->syncItems($quotation, $request);
        $this->syncSampleImages($quotation, $request);
        $quotation->recalculateTotals();

        return redirect()->route('quotations.show', $quotation)
            ->with('success', 'Quotation updated.');
    }

    public function destroy(Quotation $quotation): RedirectResponse
    {
        foreach (['sample_image_1', 'sample_image_2'] as $field) {
            if ($quotation->$field) {
                Storage::disk('public')->delete($quotation->$field);
            }
        }
        $quotation->delete();
        return redirect()->route('quotations.index')->with('success', 'Quotation deleted.');
    }

    public function pdf(Quotation $quotation): Response
    {
        $quotation->load(['customer', 'branch', 'items']);
        $settings = Setting::allKeyed();
        $tax = TaxService::resolve('quotation', $quotation->branch_id);

        // Urdu locale: DomPDF cannot shape Arabic script — use browser print page instead
        if (app()->getLocale() === 'ur') {
            return response(
                view('quotations.quotation-print', compact('quotation', 'settings', 'tax'))
            );
        }

        $fontCacheDir = storage_path('fonts');
        if (!is_dir($fontCacheDir)) {
            mkdir($fontCacheDir, 0775, true);
        }

        $pdf = Pdf::loadView('quotations.quotation-pdf', compact('quotation', 'settings', 'tax'))
            ->setPaper('a4', 'portrait');

        $filename = "quotation-{$quotation->quotation_number}.pdf";

        return env('PDF_MODE', 'download') === 'stream'
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }

    /**
     * Convert a draft quotation into a real Order (invoice). The order is created
     * as a shell — customer, total/advance carried over — ready for suits to be added.
     */
    public function convert(Quotation $quotation): RedirectResponse
    {
        if ($quotation->status === 'converted') {
            return redirect()->route('orders.show', $quotation->converted_order_id)
                ->with('error', 'This quotation was already converted.');
        }

        $order = Order::create([
            'customer_id'    => $quotation->customer_id,
            'branch_id'      => $quotation->branch_id,
            'order_number'   => Order::nextOrderNumber(),
            'order_date'     => now()->toDateString(),
            'delivery_date'  => null,
            'subtotal'        => $quotation->subtotal,
            'discount_type'   => $quotation->discount_type,
            'discount_value'  => $quotation->discount_value,
            'discount_amount' => $quotation->discount_amount,
            'tax_mode'        => $quotation->tax_mode,
            'tax_rate'        => $quotation->tax_rate,
            'tax_amount'      => $quotation->tax_amount,
            'total_amount'   => $quotation->total_amount,
            'advance_amount' => 0,
            'balance_amount' => $quotation->total_amount,
            'notes'          => trim(($quotation->notes ?? '') . ($quotation->design_reference ? "\n\nDesign Reference: {$quotation->design_reference}" : '')) ?: null,
            'extras'         => [],
        ]);

        if ($quotation->advance_amount > 0) {
            Payment::create([
                'order_id'     => $order->id,
                'branch_id'    => $order->branch_id,
                'received_by'  => auth()->id(),
                'amount'       => $quotation->advance_amount,
                'method'       => 'cash',
                'payment_date' => $order->order_date,
                'reference'    => 'INITIAL_ADVANCE',
                'note'         => 'Advance carried over from quotation ' . $quotation->quotation_number,
            ]);
        }
        $order->recalculateBalance();

        $quotation->update([
            'status'             => 'converted',
            'converted_order_id' => $order->id,
        ]);

        return redirect()->route('measurements.create', [
                'customer'    => $quotation->customer_id,
                'redirect_to' => route('orders.show', $order),
            ])
            ->with('success', "Quotation {$quotation->quotation_number} converted to Order {$order->order_number}. Add the customer's measurements to continue.");
    }

    /** Sanitised discount + tax inputs; totals are derived later by Quotation::recalculateTotals(). */
    private function taxFields(Request $request, ?int $branchId): array
    {
        $branchId = $branchId ?: $this->currentBranchId();
        $calc = TaxService::fromRequest($request, 'quotation', 0, $branchId);

        return [
            'discount_type'  => $calc['discount_type'],
            'discount_value' => $calc['discount_value'],
            'tax_mode'       => $calc['tax_mode'],
            'tax_rate'       => $calc['tax_rate'],
        ];
    }

    private function syncSampleImages(Quotation $quotation, Request $request): void
    {
        foreach (['sample_image_1', 'sample_image_2'] as $field) {
            $replace = $request->hasFile($field);
            $remove  = $request->boolean('remove_' . $field);

            if (($replace || $remove) && $quotation->$field) {
                Storage::disk('public')->delete($quotation->$field);
                $quotation->$field = null;
            }
            if ($replace) {
                $quotation->$field = $request->file($field)->store('quotation-samples', 'public');
            }
        }
        $quotation->save();
    }

    private function syncItems(Quotation $quotation, Request $request): void
    {
        $descriptions = $request->input('description', []);
        $qtys         = $request->input('qty', []);
        $rates        = $request->input('rate', []);

        $quotation->items()->delete();

        $sort = 0;
        foreach ($descriptions as $i => $description) {
            $description = trim((string) $description);
            if ($description === '') {
                continue;
            }
            $quotation->items()->create([
                'description' => $description,
                'qty'         => (float) ($qtys[$i] ?? 1),
                'rate'        => (float) ($rates[$i] ?? 0),
                'sort_order'  => $sort++,
            ]);
        }
    }
}
