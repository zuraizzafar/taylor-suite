<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\FabricSale;
use App\Models\Order;
use App\Models\Quotation;
use App\Models\Setting;
use App\Traits\HasBranchScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class TaxReportController extends Controller
{
    use HasBranchScope;

    // ─── Sales tax report ────────────────────────────────────────────────────

    public function tax(Request $request): View
    {
        return view('reports.tax', $this->taxData($request));
    }

    public function taxPdf(Request $request)
    {
        $data = $this->taxData($request) + ['settings' => Setting::allKeyed()];
        $pdf  = Pdf::loadView('reports.pdf.tax', $data)->setPaper('a4', 'landscape');

        $filename = "sales-tax-{$data['from']}-{$data['to']}.pdf";
        return env('PDF_MODE', 'download') === 'stream' ? $pdf->stream($filename) : $pdf->download($filename);
    }

    public function taxCsv(Request $request)
    {
        $d = $this->taxData($request);

        return $this->csv("sales-tax-{$d['from']}-{$d['to']}.csv", function ($out) use ($d) {
            fputcsv($out, ['Sales Tax Report', $d['from'], $d['to']]);
            fputcsv($out, []);
            fputcsv($out, ['Taxable value', $d['summary']['taxable']]);
            fputcsv($out, ['Output tax (total)', $d['summary']['output_tax']]);
            fputcsv($out, ['  collected from clients (inclusive)', $d['summary']['collected']]);
            fputcsv($out, ['  borne by us (exclusive)', $d['summary']['borne']]);
            fputcsv($out, ['Input tax (purchases)', $d['summary']['input_tax']]);
            fputcsv($out, ['Net tax payable', $d['summary']['net_payable']]);
            fputcsv($out, []);
            fputcsv($out, ['OUTPUT TAX — DOCUMENTS']);
            fputcsv($out, ['Date', 'Type', 'Number', 'Buyer', 'Buyer NTN', 'Taxable value', 'Rate %', 'Tax', 'Mode', 'Total payable']);
            foreach ($d['rows'] as $r) {
                fputcsv($out, [$r['date'], $r['type'], $r['number'], $r['buyer'], $r['buyer_ntn'], $r['taxable'], $r['rate'], $r['tax'], $r['mode'], $r['total']]);
            }
            fputcsv($out, []);
            fputcsv($out, ['INPUT TAX — PURCHASES']);
            fputcsv($out, ['Date', 'Category', 'Supplier', 'Supplier NTN/STRN', 'Invoice no.', 'Amount paid', 'Input tax']);
            foreach ($d['inputs'] as $e) {
                fputcsv($out, [$e->date->toDateString(), $e->category, $e->supplier_name, $e->supplier_ntn, $e->supplier_invoice_no, $e->amount, $e->tax_amount]);
            }
        });
    }

    private function taxData(Request $request): array
    {
        [$from, $to, $preset] = $this->resolvePeriod($request);

        $orders = Order::with(['customer', 'branch'])
            ->where('tax_mode', '!=', 'none')
            ->whereBetween('order_date', [$from, $to]);
        $this->branchQuery($orders);

        $sales = FabricSale::with('branch')
            ->where('tax_mode', '!=', 'none')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        $this->branchQuery($sales);

        $expenses = Expense::where('tax_amount', '>', 0)->whereBetween('date', [$from, $to]);
        $this->branchQuery($expenses);

        $rows = collect();
        foreach ($orders->orderBy('order_date')->get() as $o) {
            $rows->push([
                'date'      => $o->order_date->toDateString(),
                'type'      => 'Invoice',
                'number'    => $o->order_number,
                'buyer'     => $o->customer?->company_name ?: $o->customer?->name,
                'buyer_ntn' => $o->customer?->ntn,
                'taxable'   => (float) $o->subtotal - (float) $o->discount_amount,
                'rate'      => (float) $o->tax_rate,
                'tax'       => (float) $o->tax_amount,
                'mode'      => $o->tax_mode,
                'total'     => (float) $o->total_amount,
            ]);
        }
        foreach ($sales->orderBy('created_at')->get() as $s) {
            $rows->push([
                'date'      => $s->created_at->toDateString(),
                'type'      => 'Fabric sale',
                'number'    => $s->sale_code,
                'buyer'     => $s->customer_name,
                'buyer_ntn' => null,
                'taxable'   => (float) $s->subtotal,
                'rate'      => (float) $s->tax_rate,
                'tax'       => (float) $s->tax_amount,
                'mode'      => $s->tax_mode,
                'total'     => (float) $s->total_amount,
            ]);
        }
        $rows = $rows->sortBy('date')->values();

        $inputs = $expenses->orderBy('date')->get();

        $outputTax = (float) $rows->sum('tax');
        $inputTax  = (float) $inputs->sum('tax_amount');

        $summary = [
            'taxable'     => (float) $rows->sum('taxable'),
            'output_tax'  => $outputTax,
            'collected'   => (float) $rows->where('mode', 'inclusive')->sum('tax'),
            'borne'       => (float) $rows->where('mode', 'exclusive')->sum('tax'),
            'input_tax'   => $inputTax,
            'net_payable' => $outputTax - $inputTax,
        ];

        $byRate = $rows->groupBy(fn ($r) => rtrim(rtrim(number_format($r['rate'], 2), '0'), '.'))
            ->map(fn (Collection $g) => ['taxable' => $g->sum('taxable'), 'tax' => $g->sum('tax'), 'count' => $g->count()])
            ->sortKeys();

        $byType = $rows->groupBy('type')
            ->map(fn (Collection $g) => ['taxable' => $g->sum('taxable'), 'tax' => $g->sum('tax'), 'count' => $g->count()]);

        return compact('from', 'to', 'preset', 'rows', 'inputs', 'summary', 'byRate', 'byType');
    }

    // ─── Discounts report ────────────────────────────────────────────────────

    public function discounts(Request $request): View
    {
        return view('reports.discounts', $this->discountData($request));
    }

    public function discountsPdf(Request $request)
    {
        $data = $this->discountData($request) + ['settings' => Setting::allKeyed()];
        $pdf  = Pdf::loadView('reports.pdf.discounts', $data)->setPaper('a4', 'landscape');

        $filename = "discounts-{$data['from']}-{$data['to']}.pdf";
        return env('PDF_MODE', 'download') === 'stream' ? $pdf->stream($filename) : $pdf->download($filename);
    }

    public function discountsCsv(Request $request)
    {
        $d = $this->discountData($request);

        return $this->csv("discounts-{$d['from']}-{$d['to']}.csv", function ($out) use ($d) {
            fputcsv($out, ['Discounts Report', $d['from'], $d['to']]);
            fputcsv($out, []);
            fputcsv($out, ['Type', 'Date', 'Number', 'Customer', 'Subtotal', 'Discount type', 'Discount value', 'Discount amount', 'Total payable']);
            foreach ($d['rows'] as $r) {
                fputcsv($out, [$r['type'], $r['date'], $r['number'], $r['customer'], $r['subtotal'], $r['discount_type'], $r['discount_value'], $r['discount'], $r['total']]);
            }
        });
    }

    private function discountData(Request $request): array
    {
        [$from, $to, $preset] = $this->resolvePeriod($request);

        $orders = Order::with('customer')->where('discount_amount', '>', 0)->whereBetween('order_date', [$from, $to]);
        $this->branchQuery($orders);
        $quotes = Quotation::with('customer')->where('discount_amount', '>', 0)->whereBetween('quotation_date', [$from, $to]);
        $this->branchQuery($quotes);

        $allOrders = Order::whereBetween('order_date', [$from, $to]);
        $this->branchQuery($allOrders);
        $ordersGross = (float) $allOrders->sum('subtotal');

        $rows = collect();
        foreach ($orders->orderBy('order_date')->get() as $o) {
            $rows->push($this->discountRow('Invoice', $o->order_date->toDateString(), $o->order_number, $o));
        }
        foreach ($quotes->orderBy('quotation_date')->get() as $q) {
            $rows->push($this->discountRow('Quotation', $q->quotation_date->toDateString(), $q->quotation_number, $q));
        }
        $rows = $rows->sortBy('date')->values();

        $invoiceDiscount = (float) $rows->where('type', 'Invoice')->sum('discount');
        $summary = [
            'invoice_discount'   => $invoiceDiscount,
            'invoice_count'      => $rows->where('type', 'Invoice')->count(),
            'quotation_discount' => (float) $rows->where('type', 'Quotation')->sum('discount'),
            'quotation_count'    => $rows->where('type', 'Quotation')->count(),
            'pct_of_sales'       => $ordersGross > 0 ? round($invoiceDiscount / $ordersGross * 100, 2) : 0,
        ];

        return compact('from', 'to', 'preset', 'rows', 'summary');
    }

    private function discountRow(string $type, string $date, string $number, $doc): array
    {
        return [
            'type'           => $type,
            'date'           => $date,
            'number'         => $number,
            'customer'       => $doc->customer?->company_name ?: $doc->customer?->name,
            'subtotal'       => (float) $doc->subtotal,
            'discount_type'  => $doc->discount_type,
            'discount_value' => (float) $doc->discount_value,
            'discount'       => (float) $doc->discount_amount,
            'total'          => (float) $doc->total_amount,
        ];
    }

    // ─── helpers ─────────────────────────────────────────────────────────────

    private function csv(string $filename, callable $write)
    {
        $headers = [
            'Content-type'        => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma'              => 'no-cache',
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Expires'             => '0',
        ];

        return response()->stream(function () use ($write) {
            $out = fopen('php://output', 'w');
            fprintf($out, chr(0xEF) . chr(0xBB) . chr(0xBF)); // BOM so Excel reads UTF-8
            $write($out);
            fclose($out);
        }, 200, $headers);
    }

    private function resolvePeriod(Request $request): array
    {
        $preset = $request->input('preset', 'month');
        $from   = $request->input('from');
        $to     = $request->input('to');

        if (! $from || ! $to) {
            [$from, $to] = match ($preset) {
                'today'      => [today()->toDateString(), today()->toDateString()],
                'week'       => [today()->startOfWeek()->toDateString(), today()->endOfWeek()->toDateString()],
                'last_month' => [today()->subMonth()->startOfMonth()->toDateString(), today()->subMonth()->endOfMonth()->toDateString()],
                default      => [today()->startOfMonth()->toDateString(), today()->toDateString()],
            };
        }

        return [$from, $to, $preset];
    }
}
