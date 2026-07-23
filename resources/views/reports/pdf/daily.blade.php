<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'ur' ? 'ur' : 'en' }}" dir="{{ app()->getLocale() === 'ur' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }
    @if(app()->getLocale() === 'ur')
    @font-face {
        font-family: 'UrduFont';
        src: url('{{ str_replace('\\', '/', public_path('fonts/urdu.ttf')) }}') format('truetype');
        font-weight: normal;
        font-style: normal;
    }
    body { direction: rtl; text-align: right; font-family: 'UrduFont', DejaVu Sans, sans-serif !important; }
    body * { font-family: 'UrduFont', DejaVu Sans, sans-serif !important; }
    .header-right { text-align: left; }
    .footer-right { text-align: left; }
    .footer-left { text-align: right; }
    @endif

    .page { padding: 24px 28px 20px; font-family: DejaVu Sans, sans-serif; }
    .header { display: table; width: 100%; padding-bottom: 8px; border-bottom: 2px solid #1e293b; margin-bottom: 12px; }
    .header-left { display: table-cell; vertical-align: top; width: 60%; }
    .header-right { display: table-cell; vertical-align: top; text-align: right; }
    .logo-img { height: 55px; width: auto; }
    .logo-fallback { font-size: 20px; font-weight: 800; color: #0f172a; }
    .company-meta { font-size: 9px; color: #64748b; margin-top: 3px; line-height: 1.4; }

    .report-title { font-size: 18px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; }
    .report-meta { font-size: 9px; color: #475569; margin-top: 3px; }

    .kpi-row { display: table; width: 100%; margin-bottom: 12px; border-spacing: 8px 0; }
    .kpi-card { display: table-cell; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px; text-align: center; }
    .kpi-lbl { font-size: 7.5px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 2px; }
    .kpi-val { font-size: 14px; font-weight: 800; color: #0f172a; }

    table.report-table { width: 100%; border-collapse: collapse; margin-bottom: 14px; font-size: 10px; }
    table.report-table thead tr { background: #1e293b; color: #fff; }
    table.report-table thead th { padding: 6px 8px; text-align: left; font-weight: 700; font-size: 10px; }
    table.report-table tbody tr:nth-child(even) { background: #f8fafc; }
    table.report-table tbody td { padding: 5px 8px; border-bottom: 1px solid #f1f5f9; }
    table.report-table tfoot td { padding: 6px 8px; background: #f1f5f9; border-top: 1.5px solid #cbd5e1; font-weight: 700; }

    .footer { border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 8.5px; color: #94a3b8; display: table; width: 100%; page-break-inside: avoid; }
    .footer-left { display: table-cell; }
    .footer-right { display: table-cell; text-align: right; }
</style>
</head>
<body>

@php
    $companyName    = $settings['company_name']    ?? 'The Suit Tailor';
    $companyTagline = $settings['company_tagline'] ?? 'Professional Tailoring Services';
    $logoPath       = $settings['logo_path']       ?? null;
    $isUrdu         = app()->getLocale() === 'ur';

    $logoB64 = null; $logoMime = 'image/png';
    if ($logoPath && file_exists(storage_path('app/public/' . $logoPath))) {
        $logoB64  = base64_encode(file_get_contents(storage_path('app/public/' . $logoPath)));
        $logoMime = mime_content_type(storage_path('app/public/' . $logoPath));
    }

    $totalOrders  = $orders->count();
    $totalSuits   = $orders->sum(fn($o) => $o->suits->count());
    $totalBilling = $orders->sum('total_amount');
    $totalAdvance = $orders->sum('advance_amount');
    $totalBalance = $orders->sum('balance_amount');
@endphp

<div class="page">
    <div class="header">
        <div class="header-left">
            @if($logoB64)
            <img class="logo-img" src="data:{{ $logoMime }};base64,{{ $logoB64 }}" alt="{{ $companyName }}">
            @else
            <div class="logo-fallback">{{ $companyName }}</div>
            @endif
            <div class="company-meta">{{ $companyTagline }}</div>
        </div>
        <div class="header-right">
            <div class="report-title">{{ $isUrdu ? 'روزانہ کے آرڈرز کی رپورٹ' : 'DAILY ORDERS REPORT' }}</div>
            <div class="report-meta">Date: <strong>{{ \Carbon\Carbon::parse($date)->format('d M Y') }}</strong></div>
        </div>
    </div>

    <div class="kpi-row">
        <div class="kpi-card">
            <div class="kpi-lbl">Total Orders</div>
            <div class="kpi-val">{{ $totalOrders }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-lbl">Total Suits</div>
            <div class="kpi-val">{{ $totalSuits }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-lbl">Total Billing</div>
            <div class="kpi-val">Rs {{ number_format($totalBilling) }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-lbl">Advance Received</div>
            <div class="kpi-val" style="color:#16a34a">Rs {{ number_format($totalAdvance) }}</div>
        </div>
        <div class="kpi-card">
            <div class="kpi-lbl">Balance Due</div>
            <div class="kpi-val" style="color:#dc2626">Rs {{ number_format($totalBalance) }}</div>
        </div>
    </div>

    <table class="report-table">
        <thead>
            <tr>
                <th style="width:20px">#</th>
                <th>Order #</th>
                <th>Customer</th>
                <th>Mobile</th>
                <th style="text-align:center">Suits</th>
                <th style="text-align:right">Total Amount</th>
                <th style="text-align:right">Advance</th>
                <th style="text-align:right">Balance</th>
            </tr>
        </thead>
        <tbody>
            @forelse($orders as $idx => $order)
            <tr>
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight:700">{{ $order->order_number }}</td>
                <td>{{ $order->customer->name }}</td>
                <td>{{ $order->customer->mobile }}</td>
                <td style="text-align:center">{{ $order->suits->count() }}</td>
                <td style="text-align:right">Rs {{ number_format($order->total_amount) }}</td>
                <td style="text-align:right; color:#16a34a">Rs {{ number_format($order->advance_amount) }}</td>
                <td style="text-align:right; color:#dc2626; font-weight:700">Rs {{ number_format($order->balance_amount) }}</td>
            </tr>
            @empty
            <tr><td colspan="8" style="text-align:center; color:#94a3b8; padding:12px">No orders recorded on this date.</td></tr>
            @endforelse
        </tbody>
        @if($orders->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="4" style="text-align:right">Grand Totals:</td>
                <td style="text-align:center">{{ $totalSuits }}</td>
                <td style="text-align:right">Rs {{ number_format($totalBilling) }}</td>
                <td style="text-align:right; color:#16a34a">Rs {{ number_format($totalAdvance) }}</td>
                <td style="text-align:right; color:#dc2626">Rs {{ number_format($totalBalance) }}</td>
            </tr>
        </tfoot>
        @endif
    </table>

    <div class="footer">
        <div class="footer-left">{{ $companyName }} &mdash; {{ $companyTagline }}</div>
        <div class="footer-right">Printed: {{ now()->format('d M Y, h:i A') }}</div>
    </div>
</div>

</body>
</html>
