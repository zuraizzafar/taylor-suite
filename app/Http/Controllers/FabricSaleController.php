<?php

namespace App\Http\Controllers;

use App\Models\Fabric;
use App\Models\FabricSale;
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

        return view('fabric-sales.create', compact('fabric'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'fabric_id'       => ['required', 'exists:fabrics,id'],
            'customer_name'   => ['required', 'string', 'max:150'],
            'customer_mobile' => ['nullable', 'string', 'max:30'],
            'meter'           => ['required', 'numeric', 'min:0.1'],
        ]);

        $sale = DB::transaction(function () use ($data) {
            $fabric = Fabric::lockForUpdate()->findOrFail($data['fabric_id']);

            $sale = FabricSale::create([
                'fabric_id'       => $fabric->id,
                'branch_id'       => $fabric->branch_id ?? $this->currentBranchId(),
                'customer_name'   => $data['customer_name'],
                'customer_mobile' => $data['customer_mobile'] ?? null,
                'meter'           => $data['meter'],
                'rate'            => $fabric->sale_price,
                'total_amount'    => $data['meter'] * $fabric->sale_price,
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

        $pdf = Pdf::loadView('fabric-sales.invoice-pdf', compact('fabricSale', 'settings'))
            ->setPaper('a4', 'portrait');

        $filename = "invoice-{$fabricSale->sale_code}.pdf";

        return env('PDF_MODE', 'download') === 'stream'
            ? $pdf->stream($filename)
            : $pdf->download($filename);
    }
}
