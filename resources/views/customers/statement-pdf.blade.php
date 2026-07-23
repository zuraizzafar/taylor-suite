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
    .footer-right { text-align: left; }
    .footer-left { text-align: right; }
    @endif

    /* Page */
    .page { padding: 32px 36px 28px; font-family: DejaVu Sans, sans-serif; }

    /* Header */
    .header {
        display: table;
        width: 100%;
        padding-bottom: 12px;
        border-bottom: 2px solid #1e293b;
        margin-bottom: 14px;
        font-family: DejaVu Sans, sans-serif;
    }
    .header-left  { display: table-cell; vertical-align: top; width: 60%; font-family: DejaVu Sans, sans-serif; }
    .header-right { display: table-cell; vertical-align: top; text-align: right; font-family: DejaVu Sans, sans-serif; }

    .logo-fallback { font-size: 20px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; font-family: DejaVu Sans, sans-serif; }
    .logo-img      { height: 75px; width: auto; }
    .company-meta  { font-size: 9.5px; color: #64748b; margin-top: 5px; line-height: 1.6; font-family: DejaVu Sans, sans-serif; }

    .statement-title { font-size: 22px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 1.5px; font-family: DejaVu Sans, sans-serif; }
    .customer-no     { display: inline-block; background: #1e293b; color: #fff; font-size: 10px; font-weight: 700; letter-spacing: 0.5px; padding: 3px 10px; border-radius: 4px; margin-top: 4px; font-family: DejaVu Sans, sans-serif; }
    .statement-dates { font-size: 9.5px; color: #475569; margin-top: 5px; line-height: 1.7; font-family: DejaVu Sans, sans-serif; }

    /* Customer Info & Bank */
    .info-row       { display: table; width: 100%; margin-bottom: 14px; border-spacing: 10px 0; font-family: DejaVu Sans, sans-serif; }
    .info-cell      { display: table-cell; border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; vertical-align: top; font-family: DejaVu Sans, sans-serif; }
    .info-cell-title { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 4px; font-family: DejaVu Sans, sans-serif; }
    .info-cell-name  { font-size: 12px; font-weight: 700; color: #0f172a; font-family: DejaVu Sans, sans-serif; }
    .info-cell-sub   { font-size: 9px; color: #64748b; margin-top: 2px; line-height: 1.5; font-family: DejaVu Sans, sans-serif; }

    /* Summary Banner */
    .summary-banner { display: table; width: 100%; margin-bottom: 16px; background: #f8fafc; border: 1px solid #cbd5e1; border-radius: 6px; border-collapse: collapse; }
    .summary-cell   { display: table-cell; padding: 10px 12px; text-align: center; vertical-align: middle; border-right: 1px solid #cbd5e1; }
    .summary-cell:last-child { border-right: none; }
    .summary-cell-danger { background: #991b1b; color: #ffffff; }
    .summary-lbl { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #64748b; margin-bottom: 2px; }
    .summary-lbl-danger { font-size: 8.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #fca5a5; margin-bottom: 2px; }
    .summary-val { font-size: 14px; font-weight: 800; color: #0f172a; }
    .summary-val-danger { font-size: 16px; font-weight: 800; color: #ffffff; }

    /* Items table */
    table.items           { width: 100%; border-collapse: collapse; margin-bottom: 16px; font-size: 11px; font-family: DejaVu Sans, sans-serif; }
    table.items thead tr  { background: #1e293b; }
    table.items thead th  { padding: 7px 8px; font-size: 11px; font-weight: 600; text-align: left; letter-spacing: 0.3px; font-family: DejaVu Sans, sans-serif; color: #ffffff; background: #1e293b; }
    table.items tbody tr:nth-child(even) { background: #f8fafc; }
    table.items tbody td  { padding: 7px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 11px; font-family: DejaVu Sans, sans-serif; color: #1e293b; }
    table.items tfoot td  { padding: 7px 8px; background: #f1f5f9; border-top: 1.5px solid #cbd5e1; font-weight: 700; font-size: 11px; font-family: DejaVu Sans, sans-serif; color: #1e293b; }

    .pending-row { background: #fef2f2 !important; }
    .pending-text { color: #dc2626; font-weight: 700; }
    .paid-text { color: #16a34a; font-weight: 600; }

    /* Legal notice */
    .legal-box   { border: 1px solid #e2e8f0; border-left: 4px solid #1e293b; border-radius: 4px; padding: 8px 10px; margin-bottom: 16px; background: #f8fafc; }
    .legal-title { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 3px; font-family: DejaVu Sans, sans-serif; }
    .legal-text  { font-size: 9px; color: #475569; line-height: 1.5; font-family: DejaVu Sans, sans-serif; }

    /* Footer */
    .footer       { border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 8.5px; color: #94a3b8; display: table; width: 100%; font-family: DejaVu Sans, sans-serif; }
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
@endphp

<div class="page">

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
            <div class="statement-title">{{ $isUrdu ? __('Account Statement') : 'ACCOUNT STATEMENT' }}</div>
            <div><span class="customer-no">{{ $customer->file_number }}</span></div>
            <div class="statement-dates">
                {{ $isUrdu ? __('Date:') : 'Date:' }} <strong>{{ now()->format('d M Y') }}</strong>
            </div>
        </div>
    </div>

    {{-- CUSTOMER + BANK --}}
    <div class="info-row">
        <div class="info-cell" style="width:{{ ($bankName || $bankAccount) ? '46%' : '96%' }}">
            <div class="info-cell-title">{{ $isUrdu ? __('Customer Details') : 'Customer Details' }}</div>
            <div class="info-cell-name">{{ $customer->name }}</div>
            <div class="info-cell-sub">
                Mobile: <strong>{{ $customer->mobile }}</strong><br>
                File #: <strong>{{ $customer->file_number }}</strong>
                @if($customer->address)<br>Address: {{ $customer->address }}@endif
            </div>
        </div>
        @if($bankName || $bankAccount)
        <div class="info-cell" style="width:50%">
            <table style="width:100%; border-collapse:collapse; border:none; margin:0; padding:0;" dir="{{ app()->getLocale() === 'ur' ? 'rtl' : 'ltr' }}">
                <tr>
                    <td style="border:none; padding:0; vertical-align:top; text-align:{{ app()->getLocale() === 'ur' ? 'right' : 'left' }};">
                        <div class="info-cell-title">{{ $isUrdu ? __('Bank Payment Details') : 'Bank Payment Details' }}</div>
                        @if($bankName)<div class="info-cell-name" style="font-size:10.5px; margin-bottom:2px;">{{ $bankName }}</div>@endif
                        <div class="info-cell-sub">
                            @if($bankTitle)Title: <strong>{{ $bankTitle }}</strong><br>@endif
                            @if($bankAccount)Account: <strong>{{ $bankAccount }}</strong>@endif
                        </div>
                    </td>
                    @if($payQrB64)
                    <td style="border:none; padding:{{ app()->getLocale() === 'ur' ? '0 8px 0 0' : '0 0 0 8px' }}; vertical-align:middle; text-align:{{ app()->getLocale() === 'ur' ? 'left' : 'right' }}; width:65px;">
                        <img src="data:{{ $payQrMime }};base64,{{ $payQrB64 }}" alt="Payment QR" style="width:55px;height:55px;display:block;margin:0 auto 1px;">
                        <div style="font-size:6.5px;color:#94a3b8;text-align:center;">{{ $isUrdu ? __('Scan to pay') : 'Scan to pay' }}</div>
                    </td>
                    @endif
                </tr>
            </table>
        </div>
        @endif
    </div>

    {{-- SUMMARY BANNER --}}
    <div class="summary-banner">
        <div class="summary-cell" style="width:30%">
            <div class="summary-lbl">{{ $isUrdu ? __('Total Orders Value') : 'Total Orders Value' }}</div>
            <div class="summary-val">Rs {{ number_format($totalOrdersAmount) }}</div>
        </div>
        <div class="summary-cell" style="width:30%">
            <div class="summary-lbl">{{ $isUrdu ? __('Total Amount Paid') : 'Total Amount Paid' }}</div>
            <div class="summary-val" style="color:#16a34a">Rs {{ number_format($totalPaidAmount) }}</div>
        </div>
        <div class="summary-cell summary-cell-danger" style="width:40%">
            <div class="summary-lbl-danger">{{ $isUrdu ? __('Net Pending Balance Due') : 'Net Pending Balance Due' }}</div>
            <div class="summary-val-danger">Rs {{ number_format($totalBalanceAmount) }}</div>
        </div>
    </div>

    {{-- ORDERS STATEMENT TABLE --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:20px">#</th>
                <th>{{ $isUrdu ? __('Order #') : 'Order #' }}</th>
                <th>{{ $isUrdu ? __('Date') : 'Date' }}</th>
                <th>{{ $isUrdu ? __('Delivery') : 'Delivery' }}</th>
                <th>{{ $isUrdu ? __('Suits') : 'Suits' }}</th>
                <th style="text-align:right">{{ $isUrdu ? __('Total Amount') : 'Total Amount' }}</th>
                <th style="text-align:right">{{ $isUrdu ? __('Paid') : 'Paid' }}</th>
                <th style="text-align:right">{{ $isUrdu ? __('Pending Balance') : 'Pending Balance' }}</th>
            </tr>
        </thead>
        <tbody>
            @forelse($customer->orders as $idx => $order)
            @php
                $paid = max(0, $order->total_amount - $order->balance_amount);
                $isPending = $order->balance_amount > 0;
            @endphp
            <tr class="{{ $isPending ? 'pending-row' : '' }}">
                <td>{{ $idx + 1 }}</td>
                <td style="font-weight:700">{{ $order->order_number }}</td>
                <td>{{ $order->order_date->format('d M Y') }}</td>
                <td>{{ $order->delivery_date?->format('d M Y') ?? '—' }}</td>
                <td>{{ $order->suits->count() }} suit(s)</td>
                <td style="text-align:right">Rs {{ number_format($order->total_amount) }}</td>
                <td style="text-align:right" class="paid-text">Rs {{ number_format($paid) }}</td>
                <td style="text-align:right" class="{{ $isPending ? 'pending-text' : '' }}">
                    Rs {{ number_format($order->balance_amount) }}
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="8" style="text-align:center; color:#94a3b8; padding:12px;">
                    {{ $isUrdu ? __('No orders found for this customer.') : 'No orders found for this customer.' }}
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($customer->orders->isNotEmpty())
        <tfoot>
            <tr>
                <td colspan="5" style="text-align:right">{{ $isUrdu ? __('Grand Totals:') : 'Grand Totals:' }}</td>
                <td style="text-align:right">Rs {{ number_format($totalOrdersAmount) }}</td>
                <td style="text-align:right; color:#16a34a">Rs {{ number_format($totalPaidAmount) }}</td>
                <td style="text-align:right; color:#dc2626; font-size:12px; font-weight:800">
                    Rs {{ number_format($totalBalanceAmount) }}
                </td>
            </tr>
        </tfoot>
        @endif
    </table>

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
        <div class="footer-right">
            <div>{{ $isUrdu ? __('Printed:') : 'Printed:' }} {{ now()->format('d M Y, h:i A') }}</div>
        </div>
    </div>

</div>

</body>
</html>
