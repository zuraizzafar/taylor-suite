<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"></head>
<body>
@php $fmt = fn ($v) => \App\Services\TaxService::fmt($v); @endphp
<div class="page">
    @include('reports.pdf._head', ['title' => 'Sales Tax Report'])

    <div class="kpi-row">
        <div class="kpi-card"><div class="kpi-lbl">Taxable value</div><div class="kpi-val">Rs {{ $fmt($summary['taxable']) }}</div></div>
        <div class="kpi-card"><div class="kpi-lbl">Output tax</div><div class="kpi-val" style="color:#1d4ed8">Rs {{ $fmt($summary['output_tax']) }}</div>
            <div class="kpi-sub">Charged: Rs {{ $fmt($summary['collected']) }} &middot; Borne: Rs {{ $fmt($summary['borne']) }}</div></div>
        <div class="kpi-card"><div class="kpi-lbl">Input tax</div><div class="kpi-val" style="color:#7e22ce">Rs {{ $fmt($summary['input_tax']) }}</div></div>
        <div class="kpi-card" style="background:#fffbeb;border-color:#fde68a"><div class="kpi-lbl">{{ $summary['net_payable'] >= 0 ? 'Net tax payable' : 'Net refundable / carry forward' }}</div>
            <div class="kpi-val">Rs {{ $fmt(abs($summary['net_payable'])) }}</div></div>
    </div>

    <h3 class="sec">By tax rate</h3>
    <table class="report-table">
        <thead><tr><th>Rate</th><th class="r">Documents</th><th class="r">Taxable value</th><th class="r">Tax</th></tr></thead>
        <tbody>
            @forelse($byRate as $rate => $g)
            <tr><td>{{ $rate }}%</td><td class="r">{{ $g['count'] }}</td><td class="r">Rs {{ $fmt($g['taxable']) }}</td><td class="r">Rs {{ $fmt($g['tax']) }}</td></tr>
            @empty
            <tr><td colspan="4" style="text-align:center;color:#94a3b8">No taxed documents</td></tr>
            @endforelse
        </tbody>
    </table>

    <h3 class="sec">Output tax &mdash; documents</h3>
    <table class="report-table">
        <thead><tr>
            <th>Date</th><th>Number</th><th>Buyer</th><th>Buyer NTN</th>
            <th class="r">Taxable value</th><th class="r">Rate</th><th class="r">Tax</th><th>Mode</th><th class="r">Total payable</th>
        </tr></thead>
        <tbody>
            @forelse($rows as $r)
            <tr>
                <td>{{ \Carbon\Carbon::parse($r['date'])->format('d M Y') }}</td>
                <td style="font-weight:700">{{ $r['number'] }}</td>
                <td>{{ $r['buyer'] }}</td>
                <td>{{ $r['buyer_ntn'] ?: '—' }}</td>
                <td class="r">Rs {{ $fmt($r['taxable']) }}</td>
                <td class="r">{{ \App\Services\TaxService::rateLabel($r['rate']) }}</td>
                <td class="r" style="font-weight:700">Rs {{ $fmt($r['tax']) }}</td>
                <td>{{ $r['mode'] === 'inclusive' ? 'Charged' : 'Not charged' }}</td>
                <td class="r">Rs {{ $fmt($r['total']) }}</td>
            </tr>
            @empty
            <tr><td colspan="9" style="text-align:center;color:#94a3b8;padding:10px">No taxed documents in this period.</td></tr>
            @endforelse
        </tbody>
        @if($rows->isNotEmpty())
        <tfoot><tr>
            <td colspan="4" class="r">Total</td>
            <td class="r">Rs {{ $fmt($summary['taxable']) }}</td><td></td>
            <td class="r">Rs {{ $fmt($summary['output_tax']) }}</td><td></td>
            <td class="r">Rs {{ $fmt($rows->sum('total')) }}</td>
        </tr></tfoot>
        @endif
    </table>

    <h3 class="sec">Input tax &mdash; purchases</h3>
    <table class="report-table">
        <thead><tr><th>Date</th><th>Category</th><th>Supplier</th><th>Supplier NTN / STRN</th><th>Invoice no.</th><th class="r">Amount</th><th class="r">Input tax</th></tr></thead>
        <tbody>
            @forelse($inputs as $e)
            <tr>
                <td>{{ $e->date->format('d M Y') }}</td><td>{{ $e->category }}</td><td>{{ $e->supplier_name ?: '—' }}</td>
                <td>{{ $e->supplier_ntn ?: '—' }}</td><td>{{ $e->supplier_invoice_no ?: '—' }}</td>
                <td class="r">Rs {{ $fmt($e->amount) }}</td><td class="r" style="font-weight:700">Rs {{ $fmt($e->tax_amount) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:10px">No input tax recorded in this period.</td></tr>
            @endforelse
        </tbody>
        @if($inputs->isNotEmpty())
        <tfoot><tr><td colspan="5" class="r">Total</td><td class="r">Rs {{ $fmt($inputs->sum('amount')) }}</td><td class="r">Rs {{ $fmt($summary['input_tax']) }}</td></tr></tfoot>
        @endif
    </table>

    <div class="footer">Printed: {{ now()->format('d M Y, h:i A') }}</div>
</div>
</body>
</html>
