<!DOCTYPE html>
<html lang="en">
<head><meta charset="UTF-8"></head>
<body>
@php $fmt = fn ($v) => \App\Services\TaxService::fmt($v); @endphp
<div class="page">
    @include('reports.pdf._head', ['title' => 'Discounts Report'])

    <div class="kpi-row">
        <div class="kpi-card"><div class="kpi-lbl">Discount given on invoices</div><div class="kpi-val" style="color:#e11d48">Rs {{ $fmt($summary['invoice_discount']) }}</div><div class="kpi-sub">{{ $summary['invoice_count'] }} invoices</div></div>
        <div class="kpi-card"><div class="kpi-lbl">Share of invoiced sales</div><div class="kpi-val">{{ $summary['pct_of_sales'] }}%</div><div class="kpi-sub">of pre-discount value</div></div>
        <div class="kpi-card"><div class="kpi-lbl">Discount quoted</div><div class="kpi-val" style="color:#d97706">Rs {{ $fmt($summary['quotation_discount']) }}</div><div class="kpi-sub">{{ $summary['quotation_count'] }} quotations</div></div>
    </div>

    <table class="report-table">
        <thead><tr><th>Date</th><th>Type</th><th>Number</th><th>Customer</th><th class="r">Subtotal</th><th class="r">Discount</th><th class="r">Total payable</th></tr></thead>
        <tbody>
            @forelse($rows as $r)
            <tr>
                <td>{{ \Carbon\Carbon::parse($r['date'])->format('d M Y') }}</td><td>{{ $r['type'] }}</td>
                <td style="font-weight:700">{{ $r['number'] }}</td><td>{{ $r['customer'] }}</td>
                <td class="r">Rs {{ $fmt($r['subtotal']) }}</td>
                <td class="r" style="color:#e11d48;font-weight:700">- Rs {{ $fmt($r['discount']) }}@if($r['discount_type'] === 'percent') ({{ \App\Services\TaxService::rateLabel($r['discount_value']) }})@endif</td>
                <td class="r">Rs {{ $fmt($r['total']) }}</td>
            </tr>
            @empty
            <tr><td colspan="7" style="text-align:center;color:#94a3b8;padding:10px">No discounts in this period.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Printed: {{ now()->format('d M Y, h:i A') }}</div>
</div>
</body>
</html>
