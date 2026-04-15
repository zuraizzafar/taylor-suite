<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; background: #fff; }

    /* Page */
    .page { padding: 36px 40px 30px; page-break-after: always; }
    .page:last-child { page-break-after: auto; }

    /* Copy label pill */
    .copy-label-bar { margin-bottom: 18px; }
    .copy-label-bar span {
        display: inline-block;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        font-size: 8.5px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 2px;
        color: #64748b;
        padding: 3px 14px;
        border-radius: 20px;
    }

    /* Header */
    .header {
        display: table;
        width: 100%;
        padding-bottom: 14px;
        border-bottom: 2px solid #1e293b;
        margin-bottom: 16px;
    }
    .header-left  { display: table-cell; vertical-align: top; width: 60%; }
    .header-right { display: table-cell; vertical-align: top; text-align: right; }

    .logo-fallback { font-size: 20px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; }
    .logo-img      { height: 56px; width: auto; }
    .company-meta  { font-size: 9px; color: #64748b; margin-top: 4px; line-height: 1.6; }

    .invoice-title { font-size: 26px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 2px; }
    .invoice-no    { display: inline-block; background: #1e293b; color: #fff; font-size: 10px; font-weight: 700; letter-spacing: 0.5px; padding: 3px 10px; border-radius: 4px; margin-top: 5px; }
    .invoice-dates { font-size: 9.5px; color: #475569; margin-top: 6px; line-height: 1.8; }
    .invoice-dates strong { color: #1e293b; }

    /* Info boxes */
    .info-row  { display: table; width: 100%; margin-bottom: 16px; border-spacing: 12px 0; }
    .info-cell { display: table-cell; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; vertical-align: top; }
    .info-cell-title { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 5px; }
    .info-cell-name  { font-size: 12px; font-weight: 700; color: #0f172a; }
    .info-cell-sub   { font-size: 9.5px; color: #64748b; margin-top: 3px; line-height: 1.6; }

    /* Suits table */
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 10px; }
    table.items thead tr { background: #1e293b; color: #fff; }
    table.items thead th { padding: 7px 9px; font-size: 9px; font-weight: 600; text-align: left; letter-spacing: 0.3px; }
    table.items tbody tr:nth-child(even) { background: #f8fafc; }
    table.items tbody td { padding: 7px 9px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; }
    table.items tfoot td { padding: 6px 9px; background: #f1f5f9; border-top: 1.5px solid #cbd5e1; font-weight: 600; font-size: 9.5px; }

    /* Status badges */
    .st-pending   { color: #6b7280; }
    .st-cutting   { color: #d97706; }
    .st-stitching { color: #2563eb; }
    .st-ready     { color: #16a34a; }
    .st-delivered { color: #0f172a; }

    /* Payment summary */
    .payment-wrap { display: table; width: 100%; margin-bottom: 16px; }
    .payment-wrap-inner { display: table-cell; text-align: right; }
    .payment-table { width: 270px; border: 1px solid #e2e8f0; border-radius: 6px; border-collapse: collapse; }
    .payment-table td { padding: 6px 12px; font-size: 10.5px; }
    .payment-table tr { border-bottom: 1px solid #f1f5f9; }
    .payment-table tr:last-child { border-bottom: none; }
    .lbl { color: #64748b; }
    .val { text-align: right; font-weight: 600; }
    .row-advance { background: #f0fdf4; }
    .row-advance .lbl { color: #16a34a; }
    .row-advance .val { color: #16a34a; }
    .row-balance .lbl { color: #dc2626; }
    .row-balance .val { color: #dc2626; }
    .row-prev .lbl { color: #d97706; }
    .row-prev .val { color: #d97706; }
    .row-grand { background: #1e293b; }
    .row-grand .lbl { color: #cbd5e1; font-size: 10.5px; font-weight: 600; }
    .row-grand .val { color: #fbbf24; font-size: 12px; font-weight: 700; }

    /* Order notes */
    .note-box { margin-bottom: 14px; font-size: 9.5px; color: #64748b; padding: 7px 10px; background: #f8fafc; border-left: 3px solid #cbd5e1; border-radius: 3px; }

    /* Legal notice */
    .legal-box { border: 1px solid #e2e8f0; border-left: 4px solid #1e293b; border-radius: 4px; padding: 9px 12px; margin-bottom: 20px; background: #f8fafc; }
    .legal-title { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 4px; }
    .legal-text  { font-size: 9px; color: #475569; line-height: 1.6; }

    /* Footer */
    .footer { border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 8.5px; color: #94a3b8; display: table; width: 100%; }
    .footer-left  { display: table-cell; }
    .footer-right { display: table-cell; text-align: right; }
</style>
</head>
<body>

@php
    $companyName    = $settings['company_name']        ?? 'The Suit Tailor';
    $companyTagline = $settings['company_tagline']     ?? 'Professional Tailoring Services';
    $companyAddress = $settings['company_address']     ?? '';
    $companyPhone   = $settings['company_phone']       ?? '';
    $companyEmail   = $settings['company_email']       ?? '';
    $bankName       = $settings['bank_name']           ?? '';
    $bankTitle      = $settings['bank_account_title']  ?? '';
    $bankAccount    = $settings['bank_account_number'] ?? '';
    $logoPath       = $settings['logo_path']           ?? null;
    $paymentQrPath  = $settings['payment_qr_path']     ?? null;
    $legalNote      = $settings['invoice_legal_note']
                        ?? 'Payments are only accepted via the authorised bank account listed on this invoice. The shop and company are not responsible for any issues arising from payments made to any other account.';

    $grandTotal      = $order->balance_amount + $previousBalance;
    $showPreviousRow = $previousBalance > 0;

    // Logo base64
    $logoB64  = null;
    $logoMime = 'image/png';
    if ($logoPath) {
        $fullPath = storage_path('app/public/' . $logoPath);
        if (file_exists($fullPath)) {
            $logoB64  = base64_encode(file_get_contents($fullPath));
            $logoMime = mime_content_type($fullPath);
        }
    }

    // Payment QR base64
    $payQrB64  = null;
    $payQrMime = 'image/png';
    if ($paymentQrPath) {
        $qrFull = storage_path('app/public/' . $paymentQrPath);
        if (file_exists($qrFull)) {
            $payQrB64  = base64_encode(file_get_contents($qrFull));
            $payQrMime = mime_content_type($qrFull);
        }
    }

    $copies = [
        ['label' => 'Customer Copy', 'show_worker' => false],
        ['label' => 'Shop Copy',     'show_worker' => true],
    ];
@endphp

@foreach($copies as $copy)
<div class="page">

    {{-- Copy label --}}
    <div class="copy-label-bar"><span>{{ $copy['label'] }}</span></div>

    {{-- HEADER --}}
    <div class="header">
        <div class="header-left">
            @if($logoB64)
            <img class="logo-img" src="data:{{ $logoMime }};base64,{{ $logoB64 }}" alt="{{ $companyName }}">
            @else
            <div class="logo-fallback">{{ $companyName }}</div>
            @endif
            <div class="company-meta">
                {{ $companyTagline }}
                @if($companyAddress) &nbsp;|&nbsp; {{ $companyAddress }}@endif
                @if($companyPhone) &nbsp;|&nbsp; Tel: {{ $companyPhone }}@endif
                @if($companyEmail) &nbsp;|&nbsp; {{ $companyEmail }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="invoice-title">Invoice</div>
            <div><span class="invoice-no">{{ $order->order_number }}</span></div>
            <div class="invoice-dates">
                Order Date: <strong>{{ $order->order_date->format('d M Y') }}</strong><br>
                Delivery Date: <strong>{{ $order->delivery_date->format('d M Y') }}</strong><br>
                Suits: <strong>{{ $order->suits->count() }}</strong>
            </div>
        </div>
    </div>

    {{-- CUSTOMER + BANK --}}
    <div class="info-row">
        <div class="info-cell" style="width:52%">
            <div class="info-cell-title">Billed To</div>
            <div class="info-cell-name">{{ $order->customer->name }}</div>
            <div class="info-cell-sub">
                File No: <strong>{{ $order->customer->file_number }}</strong><br>
                Mobile: {{ $order->customer->mobile }}
                @if($order->customer->address)<br>{{ $order->customer->address }}@endif
            </div>
        </div>
        @if($bankName || $bankAccount)
        <div class="info-cell" style="width:44%">
            <div class="info-cell-title">Bank Payment Details</div>
            @if($bankName)<div class="info-cell-name" style="font-size:11px">{{ $bankName }}</div>@endif
            <div class="info-cell-sub">
                @if($bankTitle)Title: <strong>{{ $bankTitle }}</strong><br>@endif
                @if($bankAccount)Account: <strong>{{ $bankAccount }}</strong>@endif
            </div>
            @if($payQrB64)
            <div style="margin-top:6px">
                <img src="data:{{ $payQrMime }};base64,{{ $payQrB64 }}" alt="Payment QR" style="width:60px;height:60px">
                <div style="font-size:8px;color:#94a3b8;margin-top:2px">Scan to pay</div>
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- SUITS TABLE --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:24px">#</th>
                <th>Suit Code</th>
                <th>Type</th>
                <th>Fabric</th>
                @if($copy['show_worker'])<th>Worker</th>@endif
                <th style="width:70px">Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->suits as $i => $suit)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $suit->suit_code }}</strong></td>
                <td>{{ $suit->suit_type }}</td>
                <td>
                    {{ $suit->fabric_meter }}m
                    @if($suit->fabric_description)
                    <br><span style="font-size:8.5px;color:#94a3b8">{{ $suit->fabric_description }}</span>
                    @endif
                </td>
                @if($copy['show_worker'])<td>{{ $suit->worker?->name ?? '—' }}</td>@endif
                <td class="st-{{ $suit->status }}"><strong>{{ ucfirst($suit->status) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="{{ $copy['show_worker'] ? 5 : 4 }}" style="text-align:right">Total Suits:</td>
                <td>{{ $order->suits->count() }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- PAYMENT SUMMARY --}}
    <div class="payment-wrap">
        <div class="payment-wrap-inner">
        <table class="payment-table">
            <tr>
                <td class="lbl">Total Amount</td>
                <td class="val">Rs {{ number_format($order->total_amount) }}</td>
            </tr>
            <tr class="row-advance">
                <td class="lbl">Advance Paid</td>
                <td class="val">Rs {{ number_format($order->advance_amount) }}</td>
            </tr>
            <tr class="row-balance">
                <td class="lbl">Balance Due (this order)</td>
                <td class="val">Rs {{ number_format($order->balance_amount) }}</td>
            </tr>
            @if($showPreviousRow)
            <tr class="row-prev">
                <td class="lbl">Previous Dues</td>
                <td class="val">Rs {{ number_format($previousBalance) }}</td>
            </tr>
            @endif
            <tr class="row-grand">
                <td class="lbl">Grand Total Owed</td>
                <td class="val">Rs {{ number_format($grandTotal) }}</td>
            </tr>
        </table>
        </div>
    </div>
    @if($order->notes)
    <div class="note-box"><strong>Note:</strong> {{ $order->notes }}</div>
    @endif

    {{-- LEGAL NOTICE --}}
    <div class="legal-box">
        <div class="legal-title">Payment Notice</div>
        <div class="legal-text">{{ $legalNote }}</div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-left">{{ $companyName }} &mdash; {{ $companyTagline }}</div>
        <div class="footer-right">Printed: {{ now()->format('d M Y, h:i A') }}</div>
    </div>

</div>
@endforeach

</body>
</html>
