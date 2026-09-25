<?php

namespace App\Http\Controllers;

use App\Models\Fabric;
use App\Models\FabricSale;
use App\Services\TaxService;
use App\Traits\HasBranchScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\View\View;

class FabricSaleController extends Controller
{
    use HasBranchScope;

    public function create(Request $request): View
    {
        $fabric = null;
        if ($roll = $request->input('roll')) {
            $fabric = Fabric::where('roll_number', $roll)->first();
        }

        $taxInit = TaxService::formInit('fabric_sale', $this->currentBranchId());

        return view('fabric-sales.create', compact('fabric', 'taxInit'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fabric_id'       => ['required', 'exists:fabrics,id'],
            'customer_name'   => ['required', 'string', 'max:150'],
            'customer_mobile' => ['nullable', 'string', 'max:30'],
            'meter'           => ['required', 'numeric', 'min:0.1'],
            'tax_mode'        => ['nullable', 'in:none,exclusive,inclusive'],
            'tax_rate'        => ['nullable', 'numeric', 'min:0', 'max:100'],
        ]);

        $sale = DB::transaction(function () use ($data, $request) {
            $fabric = Fabric::lockForUpdate()->findOrFail($data['fabric_id']);

            $branchId = $fabric->branch_id ?? $this->currentBranchId();
            $calc = TaxService::fromRequest(
                $request, 'fabric_sale', round($data['meter'] * $fabric->sale_price, 2), $branchId, false
            );

            $sale = FabricSale::create([
                'fabric_id'       => $fabric->id,
                'branch_id'       => $fabric->branch_id ?? $this->currentBranchId(),
                'customer_name'   => $data['customer_name'],
                'customer_mobile' => $data['customer_mobile'] ?? null,
                'meter'           => $data['meter'],
                'rate'            => $fabric->sale_price,
                'subtotal'        => $calc['subtotal'],
                'tax_mode'        => $calc['tax_mode'],
                'tax_rate'        => $calc['tax_rate'],
                'tax_amount'      => $calc['tax_amount'],
                'total_amount'    => $calc['total_amount'],
                'sale_code'       => 'FS-' . str_pad((string) (FabricSale::max('id') + 1), 6, '0', STR_PAD_LEFT),
                'sold_by'         => auth()->id(),
            ]);

            $fabric->deduct((float) $data['meter'], 'fabric_sale', $sale->id, "Sold to {$data['customer_name']}");

            return $sale;
        });

        return redirect()->route('fabric-sales.invoice', $sale)->with('success', "Sale {$sale->sale_code} recorded.");
    }

    public function invoice(FabricSale $fabricSale): Response
    {
        $fabricSale->load('fabric');
        $settings = \App\Models\Setting::allKeyed();
        $tax      = TaxService::resolve('fabric_sale', $fabricSale->branch_id);

        $pdf = Pdf::loadView('fabric-sales.invoice-pdf', compact('fabricSale', 'settings', 'tax'))
            ->setPaper('a4', 'portrait');

        $filename = "invoice-{$fabricSale->sale_code}.pdf";

        return env('PDF_MODE', 'download') === 'stream'
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }
}
