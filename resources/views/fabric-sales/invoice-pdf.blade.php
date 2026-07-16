<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'ur' ? 'ur' : 'en' }}" dir="{{ app()->getLocale() === 'ur' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; background: #fff; }
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
    .payment-wrap-inner { text-align: left; }
    .footer-right { text-align: left; }
    .footer-left { text-align: right; }
    @endif

    /* Page */
    .page { padding: 36px 40px 30px; page-break-after: always; font-family: DejaVu Sans, sans-serif; }
    .page:last-child { page-break-after: auto; }

    /* Copy label pill */
    .copy-label-bar { margin-bottom: 18px; }
    .copy-label-bar span {
        display: inline-block;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        font-size: 8.5px;
        font-weight: 700;
        font-family: DejaVu Sans, sans-serif;
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
        font-family: DejaVu Sans, sans-serif;
    }
    .header-left  { display: table-cell; vertical-align: top; width: 60%; font-family: DejaVu Sans, sans-serif; }
    .header-right { display: table-cell; vertical-align: top; text-align: right; font-family: DejaVu Sans, sans-serif; }

    .logo-fallback { font-size: 22px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; font-family: DejaVu Sans, sans-serif; }
    .logo-img      { height: 80px; width: auto; }
    .company-meta  { font-size: 10px; color: #64748b; margin-top: 6px; line-height: 1.7; font-family: DejaVu Sans, sans-serif; }

    .invoice-title  { font-size: 26px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 2px; font-family: DejaVu Sans, sans-serif; }
    .invoice-no     { display: inline-block; background: #1e293b; color: #fff; font-size: 10px; font-weight: 700; letter-spacing: 0.5px; padding: 3px 10px; border-radius: 4px; margin-top: 5px; font-family: DejaVu Sans, sans-serif; }
    .invoice-dates  { font-size: 9.5px; color: #475569; margin-top: 6px; line-height: 1.8; font-family: DejaVu Sans, sans-serif; }
    .invoice-dates strong { color: #1e293b; font-family: DejaVu Sans, sans-serif; }

    /* Info boxes */
    .info-row       { display: table; width: 100%; margin-bottom: 16px; border-spacing: 12px 0; font-family: DejaVu Sans, sans-serif; }
    .info-cell      { display: table-cell; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 12px; vertical-align: top; font-family: DejaVu Sans, sans-serif; }
    .info-cell-title { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 5px; font-family: DejaVu Sans, sans-serif; }
    .info-cell-name  { font-size: 12px; font-weight: 700; color: #0f172a; font-family: DejaVu Sans, sans-serif; }
    .info-cell-sub   { font-size: 9.5px; color: #64748b; margin-top: 3px; line-height: 1.6; font-family: DejaVu Sans, sans-serif; }

    /* Items table */
    table.items           { width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 12px; font-family: DejaVu Sans, sans-serif; }
    table.items thead tr  { background: #1e293b; }
    table.items thead th  { padding: 8px 10px; font-size: 14px; font-weight: 600; text-align: left; letter-spacing: 0.3px; font-family: DejaVu Sans, sans-serif; color: #ffffff; background: #1e293b; }
    table.items tbody tr:nth-child(even) { background: #f8fafc; }
    table.items tbody td  { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 12px; font-family: DejaVu Sans, sans-serif; color: #1e293b; }
    table.items tfoot td  { padding: 7px 10px; background: #f1f5f9; border-top: 1.5px solid #cbd5e1; font-weight: 600; font-size: 11px; font-family: DejaVu Sans, sans-serif; color: #1e293b; }

    /* Payment summary */
    .payment-wrap       { display: table; width: 100%; margin-bottom: 16px; }
    .payment-wrap-inner { display: table-cell; text-align: right; }
    .payment-table      { width: 230px; border: 1px solid #e2e8f0; border-radius: 6px; border-collapse: collapse; font-family: DejaVu Sans, sans-serif; }
    .payment-table td   { padding: 4px 10px; font-size: 9.5px; font-family: DejaVu Sans, sans-serif; color: #1e293b; }
    .payment-table tr   { border-bottom: 1px solid #f1f5f9; }
    .payment-table tr:last-child { border-bottom: none; }
    .lbl { color: #64748b; font-family: DejaVu Sans, sans-serif; }
    .val { text-align: right; font-weight: 600; font-family: DejaVu Sans, sans-serif; }
    .row-advance    { background: #f0fdf4; }
    .row-balance    { }
    .row-prev       { }
    .row-grand      { background: #1e293b; }

    /* Legal notice */
    .legal-box   { border: 1px solid #e2e8f0; border-left: 4px solid #1e293b; border-radius: 4px; padding: 9px 12px; margin-bottom: 20px; background: #f8fafc; }
    .legal-title { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 4px; font-family: DejaVu Sans, sans-serif; }
    .legal-text  { font-size: 9.5px; color: #475569; line-height: 1.6; font-family: DejaVu Sans, sans-serif; }

    /* Footer */
    .footer       { border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 9px; color: #94a3b8; display: table; width: 100%; font-family: DejaVu Sans, sans-serif; }
    .footer-left  { display: table-cell; font-family: DejaVu Sans, sans-serif; }
    .footer-right { display: table-cell; text-align: right; font-family: DejaVu Sans, sans-serif; }
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

    $isUrdu = app()->getLocale() === 'ur';
    $legalNote      = $isUrdu
                        ? ($settings['invoice_legal_note_ur'] ?? ($settings['invoice_legal_note'] ?? 'ادائیگی صرف اس انوائس پر درج مجاز بینک اکاؤنٹ کے ذریعے قبول کی جاتی ہے۔ کسی اور اکاؤنٹ پر کی گئی ادائیگی کی ذمہ داری دکان یا کمپنی پر نہیں ہوگی۔'))
                        : ($settings['invoice_legal_note_en'] ?? ($settings['invoice_legal_note'] ?? 'Payments are only accepted via the authorised bank account listed on this invoice. The shop and company are not responsible for any issues arising from payments made to any other account.'));

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
        ['label' => $isUrdu ? __('Customer Copy') : 'Customer Copy'],
        ['label' => $isUrdu ? __('Shop Copy')     : 'Shop Copy'],
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
            <div class="invoice-title">{{ $isUrdu ? __('Invoice') : 'Invoice' }}</div>
            <div><span class="invoice-no">{{ $fabricSale->sale_code }}</span></div>
            <div class="invoice-dates">
                {{ $isUrdu ? __('Date:') : 'Date:' }} <strong>{{ $fabricSale->created_at->format('d M Y') }}</strong>
            </div>
        </div>
    </div>

    {{-- CUSTOMER + BANK --}}
    <div class="info-row">
        <div class="info-cell" style="width:{{ ($bankName || $bankAccount) ? '44%' : '96%' }}">
            <div class="info-cell-title">{{ $isUrdu ? __('Billed To') : 'Billed To' }}</div>
            <div class="info-cell-name">{{ $fabricSale->customer_name }}</div>
            <div class="info-cell-sub">
                Mobile: {{ $fabricSale->customer_mobile ?? '—' }}
            </div>
        </div>
        @if($bankName || $bankAccount)
        <div class="info-cell" style="width:52%">
            <table style="width:100%; border-collapse:collapse; border:none; margin:0; padding:0;" dir="{{ app()->getLocale() === 'ur' ? 'rtl' : 'ltr' }}">
                <tr>
                    <td style="border:none; padding:0; vertical-align:top; text-align:{{ app()->getLocale() === 'ur' ? 'right' : 'left' }};">
                        <div class="info-cell-title">{{ $isUrdu ? __('Bank Payment Details') : 'Bank Payment Details' }}</div>
                        @if($bankName)<div class="info-cell-name" style="font-size:11px; margin-bottom:4px;">{{ $bankName }}</div>@endif
                        <div class="info-cell-sub">
                            @if($bankTitle)Title: <strong>{{ $bankTitle }}</strong><br>@endif
                            @if($bankAccount)Account: <strong>{{ $bankAccount }}</strong>@endif
                        </div>
                    </td>
                    @if($payQrB64)
                    <td style="border:none; padding:{{ app()->getLocale() === 'ur' ? '0 10px 0 0' : '0 0 0 10px' }}; vertical-align:middle; text-align:{{ app()->getLocale() === 'ur' ? 'left' : 'right' }}; width:75px;">
                        <img src="data:{{ $payQrMime }};base64,{{ $payQrB64 }}" alt="Payment QR" style="width:65px;height:65px;display:block;margin:0 auto 2px;">
                        <div style="font-size:7px;color:#94a3b8;text-align:center;">{{ $isUrdu ? __('Scan to pay') : 'Scan to pay' }}</div>
                    </td>
                    @endif
                </tr>
            </table>
        </div>
        @endif
    </div>

    {{-- ITEMS TABLE --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:24px">#</th>
                <th>{{ $isUrdu ? __('Fabric Type') : 'Fabric Type' }}</th>
                <th>{{ $isUrdu ? __('Roll Number') : 'Roll Number' }}</th>
                <th>{{ $isUrdu ? __('Meters') : 'Meters' }}</th>
                <th>{{ $isUrdu ? __('Rate') : 'Rate' }}</th>
                <th>{{ $isUrdu ? __('Total') : 'Total' }}</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>1</td>
                <td>{{ $fabricSale->fabric->fabric_type }} ({{ $fabricSale->fabric->color }})</td>
                <td>{{ $fabricSale->fabric->roll_number }}</td>
                <td>{{ number_format($fabricSale->meter, 2) }}m</td>
                <td>Rs {{ number_format($fabricSale->rate) }}</td>
                <td>Rs {{ number_format($fabricSale->total_amount) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- PAYMENT SUMMARY --}}
    <div class="payment-wrap" style="display: table; width: 100%; margin-bottom: 16px;">
        <div style="display: table-cell; vertical-align: bottom; text-align: left; width: 50%;">
            <!-- Left side empty spacing -->
        </div>
        <div class="payment-wrap-inner" style="display: table-cell; vertical-align: top; text-align: right; width: 50%;">
            <table class="payment-table" style="display: inline-table; width: 230px; border-collapse: collapse;">
                <tr>
                    <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#64748b">{{ $isUrdu ? __('Total Amount') : 'Total Amount' }}</td>
                    <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#1e293b">Rs {{ number_format($fabricSale->total_amount) }}</td>
                </tr>
                <tr class="row-advance">
                    <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#16a34a">{{ $isUrdu ? __('Amount Paid') : 'Amount Paid' }}</td>
                    <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#16a34a">Rs {{ number_format($fabricSale->total_amount) }}</td>
                </tr>
                <tr class="row-balance">
                    <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#dc2626">{{ $isUrdu ? __('Balance Due') : 'Balance Due' }}</td>
                    <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#dc2626">Rs 0</td>
                </tr>
                <tr class="row-grand">
                    <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#e2e8f0;font-weight:700">{{ $isUrdu ? __('Grand Total') : 'Grand Total' }}</td>
                    <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#fbbf24;font-weight:800;font-size:11.5px">Rs {{ number_format($fabricSale->total_amount) }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- LEGAL NOTICE --}}
    <div class="legal-box">
        <div class="legal-title">{{ $isUrdu ? __('Payment Notice') : 'Payment Notice' }}</div>
        <div class="legal-text">{{ $legalNote }}</div>
    </div>

    {{-- FOOTER --}}
    <div class="footer">
        <div class="footer-left">
            {{ $companyName }} &mdash; {{ $companyTagline }}
        </div>
        <div class="footer-right" style="text-align:right">
            <div style="margin-bottom:3px;font-size:8.5px;color:#94a3b8">{{ $isUrdu ? __('Printed:') : 'Printed:' }} {{ now()->format('d M Y, h:i A') }}</div>
        </div>
    </div>

</div>
@endforeach

</body>
</html>
