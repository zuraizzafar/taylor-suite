<!DOCTYPE html>
<html lang="{{ app()->getLocale() === 'ur' ? 'ur' : 'en' }}" dir="{{ app()->getLocale() === 'ur' ? 'rtl' : 'ltr' }}">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 12px; color: #1e293b; background: #fff; }
    @if(app()->getLocale() === 'ur')
    @@font-face {
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

    /* Suits table */
    table.items           { width: 100%; border-collapse: collapse; margin-bottom: 18px; font-size: 12px; font-family: DejaVu Sans, sans-serif; }
    table.items thead tr  { background: #1e293b; }
    table.items thead th  { padding: 8px 10px; font-size: 14px; font-weight: 600; text-align: left; letter-spacing: 0.3px; font-family: DejaVu Sans, sans-serif; color: #ffffff; background: #1e293b; }
    table.items tbody tr:nth-child(even) { background: #f8fafc; }
    table.items tbody td  { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 12px; font-family: DejaVu Sans, sans-serif; color: #1e293b; }
    table.items tfoot td  { padding: 7px 10px; background: #f1f5f9; border-top: 1.5px solid #cbd5e1; font-weight: 600; font-size: 11px; font-family: DejaVu Sans, sans-serif; color: #1e293b; }

    /* Status badges */
    .st-pending   { color: #6b7280; font-family: DejaVu Sans, sans-serif; }
    .st-cutting   { color: #d97706; font-family: DejaVu Sans, sans-serif; }
    .st-stitching { color: #2563eb; font-family: DejaVu Sans, sans-serif; }
    .st-ready     { color: #16a34a; font-family: DejaVu Sans, sans-serif; }
    .st-delivered { color: #0f172a; font-family: DejaVu Sans, sans-serif; }

    /* Payment summary */
    .payment-wrap       { display: table; width: 100%; margin-bottom: 16px; }
    .payment-wrap-inner { display: table-cell; text-align: right; }
    .payment-table      { width: 270px; border: 1px solid #e2e8f0; border-radius: 6px; border-collapse: collapse; font-family: DejaVu Sans, sans-serif; }
    .payment-table td   { padding: 6px 12px; font-size: 11px; font-family: DejaVu Sans, sans-serif; color: #1e293b; }
    .payment-table tr   { border-bottom: 1px solid #f1f5f9; }
    .payment-table tr:last-child { border-bottom: none; }
    .lbl { color: #64748b; font-family: DejaVu Sans, sans-serif; }
    .val { text-align: right; font-weight: 600; font-family: DejaVu Sans, sans-serif; }
    .row-advance    { background: #f0fdf4; }
    .row-balance    { }
    .row-prev       { }
    .row-grand      { background: #1e293b; }

    /* Measurements */
    .meas-wrap        { border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 16px; overflow: hidden; font-family: DejaVu Sans, sans-serif; }
    .meas-title-row   { background: #f1f5f9; padding: 5px 10px; font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #475569; font-family: DejaVu Sans, sans-serif; }
    .meas-section-hdr { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #94a3b8; padding: 5px 10px 3px; background: #fff; font-family: DejaVu Sans, sans-serif; border-top: 1px solid #f1f5f9; }
    table.meas-grid   { width: 100%; border-collapse: collapse; font-family: DejaVu Sans, sans-serif; }
    table.meas-grid td { padding: 3px 8px 4px; font-size: 9.5px; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; font-family: DejaVu Sans, sans-serif; color: #1e293b; }
    table.meas-grid td:last-child { border-right: none; }
    .meas-lbl  { color: #94a3b8; font-size: 7.5px; display: block; font-family: DejaVu Sans, sans-serif; margin-bottom: 1px; }
    .meas-val  { font-weight: 700; color: #0f172a; font-size: 10px; font-family: DejaVu Sans, sans-serif; }
    .meas-empty { color: #cbd5e1; }

    /* Order notes */
    .note-box   { margin-bottom: 14px; font-size: 10px; color: #64748b; padding: 7px 10px; background: #f8fafc; border-left: 3px solid #cbd5e1; border-radius: 3px; font-family: DejaVu Sans, sans-serif; }
    .note-box strong { font-family: DejaVu Sans, sans-serif; color: #1e293b; }

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
    // Resolve measurement: prefer customer's own, fall back to first suit's measurement
    $measurement = $order->customer->measurements->first()
                   ?? $order->suits->pluck('measurement')->filter()->first();

    // Fields to display
    $qFields = [
        'q_length'      => 'Length',
        'q_shoulder'    => 'Shoulder',
        'q_chest'       => 'Chest',
        'q_waist'       => 'Waist',
        'q_seat'        => 'Seat',
        'q_sleeve'      => 'Sleeve',
        'q_sleeve_width'=> 'Slv. W',
        'q_collar'      => 'Collar',
        'q_front'       => 'Front',
        'q_back'        => 'Back',
        'q_armhole'     => 'Armhole',
        'q_cuff'        => 'Cuff',
    ];
    $sFields = [
        's_length'  => 'Length',
        's_waist'   => 'Waist',
        's_seat'    => 'Seat',
        's_thigh'   => 'Thigh',
        's_knee'    => 'Knee',
        's_bottom'  => 'Bottom',
        's_crotch'  => 'Crotch',
        's_ankle'   => 'Ankle',
    ];

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

    $grandTotal      = $order->balance_amount + $previousBalance;
    $showPreviousRow = $previousBalance > 0;
    $trackingUrl = route('tracking.show', ['tracking' => $order->order_number]);
    // Generate QR code as base64 PNG for embedding in PDF
    $trackingQrB64 = base64_encode(\SimpleSoftwareIO\QrCode\Facades\QrCode::format('png')->size(80)->errorCorrection('M')->generate($trackingUrl));
    $styleMetaLabels = [
        'collar_style'     => $isUrdu ? 'کالر کا انداز' : 'Neck / Collar Style',
        'cuff_style'       => $isUrdu ? 'کف / آستین کا انداز' : 'Cuff / Arm Style',
        'button_type'      => $isUrdu ? 'بٹن کی قسم' : 'Button Type',
        'button_count'     => $isUrdu ? 'بٹن کی تعداد' : 'Number of Buttons',
        'ghera_style'      => $isUrdu ? 'گھیرا' : 'Ghera (Bottom)',
        'stitching_style'  => $isUrdu ? 'سلائی کا انداز' : 'Stitching Style',
        'chak_patti'       => $isUrdu ? 'چاک پٹی' : 'Chak Patti',
        'kaj_hale'         => $isUrdu ? 'کاج ہال' : 'Kaj Hale',
        'pahuncha_style'   => $isUrdu ? 'پانچہ' : 'Pahuncha',
        'front_patti_size' => $isUrdu ? 'فرنٹ پٹی سائز' : 'Front Patti Size',
        'design_number'    => $isUrdu ? 'ڈیزائن نمبر' : 'Design Number',
        'fashion_style'    => $isUrdu ? 'فیشن اسٹائل' : 'Fashion Style',
    ];
    $styleOptions = collect($measurement?->meta ?? [])->filter(fn ($value) => filled($value));

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
        ['label' => $isUrdu ? __('Customer Copy') : 'Customer Copy', 'show_worker' => false],
        ['label' => $isUrdu ? __('Shop Copy')     : 'Shop Copy',     'show_worker' => true],
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
            <div><span class="invoice-no">{{ $order->order_number }}</span></div>
            <div class="invoice-dates">
                {{ $isUrdu ? __('Order Date:') : 'Order Date:' }} <strong>{{ $order->order_date->format('d M Y') }}</strong><br>
                {{ $isUrdu ? __('Delivery Date:') : 'Delivery Date:' }} <strong>{{ $order->delivery_date?->format('d M Y') ?? '—' }}</strong><br>
                {{ $isUrdu ? __('Suits:') : 'Suits:' }} <strong>{{ $order->suits->count() }}</strong>
            </div>
        </div>
    </div>

    {{-- CUSTOMER + BANK --}}
    <div class="info-row">
        <div class="info-cell" style="width:{{ ($bankName || $bankAccount) ? '44%' : '96%' }}">
            <div class="info-cell-title">{{ $isUrdu ? __('Billed To') : 'Billed To' }}</div>
            <div class="info-cell-name">{{ $order->customer->name }}</div>
            <div class="info-cell-sub">
                File No: <strong>{{ $order->customer->file_number }}</strong><br>
                Mobile: {{ $order->customer->mobile }}
                @if($order->customer->address)<br>{{ $order->customer->address }}@endif
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

    {{-- SUITS TABLE --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:24px">#</th>
                <th>{{ $isUrdu ? __('Suit Code') : 'Suit Code' }}</th>
                <th>{{ $isUrdu ? __('Type') : 'Type' }}</th>
                <th>{{ $isUrdu ? __('Fabric') : 'Fabric' }}</th>
                @if($copy['show_worker'])<th>{{ $isUrdu ? __('Worker') : 'Worker' }}</th>@endif
                <th style="width:70px">{{ $isUrdu ? __('Status') : 'Status' }}</th>
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
                <td colspan="{{ $copy['show_worker'] ? 5 : 4 }}" style="text-align:right">{{ $isUrdu ? __('Total Suits') : 'Total Suits' }}:</td>
                <td>{{ $order->suits->count() }}</td>
            </tr>
        </tfoot>
    </table>

    @php
        $measType = $measurement->type ?? 'shalwar_kameez';
        $measSections = [];
        if ($measurement) {
            if ($measType === 'waistcoat') {
                $measSections = [
                    [
                        'title' => $isUrdu ? 'واسٹ کوٹ کی پیمائش (Waistcoat)' : 'Waistcoat Measurements',
                        'fields' => [
                            'q_length'   => $isUrdu ? 'لمبائی' : 'Length',
                            'q_chest'    => $isUrdu ? 'چھاتی' : 'Chest',
                            'q_waist'    => $isUrdu ? 'کمر' : 'Waist',
                            'q_shoulder' => $isUrdu ? 'تیرا' : 'Shoulder',
                            'q_collar'   => $isUrdu ? 'کالر' : 'Collar',
                            'q_armhole'  => $isUrdu ? 'موڈہ' : 'Armhole',
                        ],
                        'col_width' => '16.66%'
                    ]
                ];
            } elseif ($measType === 'pent_coat') {
                $measSections = [
                    [
                        'title' => $isUrdu ? 'کوٹ کی پیمائش (Coat)' : 'Coat Measurements',
                        'fields' => [
                            'q_chest'    => $isUrdu ? 'چھاتی' : 'Chest',
                            'q_waist'    => $isUrdu ? 'کمر' : 'Waist',
                            'q_shoulder' => $isUrdu ? 'تیرا' : 'Shoulder',
                            'q_back'     => $isUrdu ? 'کراس بیک' : 'Cross Back',
                            'q_length'   => $isUrdu ? 'کوٹ لمبائی' : 'Coat Length',
                            'q_sleeve'   => $isUrdu ? 'بازو' : 'Sleeve',
                        ],
                        'col_width' => '16.66%'
                    ],
                    [
                        'title' => $isUrdu ? 'پینٹ کی پیمائش (Pant)' : 'Pant Measurements',
                        'fields' => [
                            's_length'   => $isUrdu ? 'پینٹ لمبائی' : 'Pant Length',
                            's_crotch'   => $isUrdu ? 'آسن' : 'In Side',
                            's_waist'    => $isUrdu ? 'کمر' : 'Waist (Pants)',
                            's_seat'     => $isUrdu ? 'ہپس' : 'Hipps',
                            's_thigh'    => $isUrdu ? 'ران' : 'Thigh',
                            's_bottom'   => $isUrdu ? 'پانچہ' : 'Bottom',
                            's_ankle'    => $isUrdu ? 'بیک پاکٹ' : 'Back Pocket',
                        ],
                        'col_width' => '14.28%'
                    ]
                ];
            } else {
                $measSections = [
                    [
                        'title' => $isUrdu ? 'قمیض کی پیمائش (Qameez)' : 'Qameez / Kameez',
                        'fields' => [
                            'q_length'      => $isUrdu ? 'لمبائی' : 'Length',
                            'q_shoulder'    => $isUrdu ? 'تیرا' : 'Shoulder',
                            'q_collar'      => $isUrdu ? 'کالر' : 'Collar',
                            'q_sleeve'      => $isUrdu ? 'بازو' : 'Sleeve',
                            'q_armhole'     => $isUrdu ? 'موڈہ' : 'Armhole',
                            'q_cuff'        => $isUrdu ? 'کف' : 'Cuff',
                            'q_chest'       => $isUrdu ? 'چھاتی' : 'Chest',
                            'q_waist'       => $isUrdu ? 'کمر' : 'Waist',
                            'q_seat'        => $isUrdu ? 'گھیرا' : 'Hips',
                            'q_sleeve_width'=> $isUrdu ? 'بازو چوڑائی' : 'Slv. W',
                            'q_front'       => $isUrdu ? 'سامنے' : 'Front',
                            'q_back'        => $isUrdu ? 'بیک' : 'Back',
                        ],
                        'col_width' => '8.33%'
                    ],
                    [
                        'title' => $isUrdu ? 'شلوار کی پیمائش (Shalwar)' : 'Shalwar / Trouser',
                        'fields' => [
                            's_length'  => $isUrdu ? 'لمبائی' : 'Length',
                            's_bottom'  => $isUrdu ? 'پانچہ' : 'Bottom',
                            's_seat'    => $isUrdu ? 'گھیرا' : 'Seat',
                            's_crotch'  => $isUrdu ? 'آسن' : 'Crotch',
                            's_waist'   => $isUrdu ? 'کمر' : 'Waist',
                            's_thigh'   => $isUrdu ? 'ران' : 'Thigh',
                            's_knee'    => $isUrdu ? 'گھٹنا' : 'Knee',
                            's_ankle'   => $isUrdu ? 'ٹخنہ' : 'Ankle',
                        ],
                        'col_width' => '12.5%'
                    ]
                ];
            }
        }
        $hasMeas = !empty($measSections);
        $measurementNotes = $measurement?->notes;
    @endphp
    @if($hasMeas)
    <div class="meas-wrap">
        <div class="meas-title-row">
            {{ $isUrdu ? __('Customer Measurements') : 'Customer Measurements' }}
            @if($measurementNotes)
            &nbsp;&middot;&nbsp; <span style="font-weight:400;color:#64748b;text-transform:none;letter-spacing:0">{{ $measurementNotes }}</span>
            @endif
        </div>

        @foreach($measSections as $section)
        <div class="meas-section-hdr">{{ $section['title'] }}</div>
        <table class="meas-grid">
            <tr>
                @foreach($section['fields'] as $field => $label)
                <td style="width:{{ $section['col_width'] }}">
                    <span class="meas-lbl">{{ $label }}</span>
                    @if(!empty($measurement->$field))
                    <span class="meas-val">{{ $measurement->$field }}</span>
                    @else
                    <span class="meas-val meas-empty">&mdash;</span>
                    @endif
                </td>
                @endforeach
            </tr>
        </table>
        @endforeach
    </div>
    @endif

    {{-- PAYMENT SUMMARY --}}
    <div class="payment-wrap" style="display: table; width: 100%; margin-bottom: 16px;">
        @if(app()->getLocale() === 'ur')
            <div style="display: table-cell; vertical-align: bottom; text-align: right; width: 50%;">
                <div style="display: inline-block; text-align: center;">
                    <img src="data:image/png;base64,{{ $trackingQrB64 }}" alt="Track order QR" style="width:70px;height:70px;display:block;margin:0 auto 4px;">
                    <span style="font-size:7.5px;color:#94a3b8;font-family:DejaVu Sans,sans-serif">ٹریکنگ کیلئے اسکین کریں</span>
                </div>
            </div>
            <div class="payment-wrap-inner" style="display: table-cell; vertical-align: top; text-align: left; width: 50%;">
                <table class="payment-table" style="display: inline-table; width: 270px; text-align: right; border-collapse: collapse;">
                    <tr>
                        <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#64748b">{{ $isUrdu ? __('Total Amount') : 'Total Amount' }}</td>
                        <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#1e293b">Rs {{ number_format($order->total_amount) }}</td>
                    </tr>
                    <tr class="row-advance">
                        <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#16a34a">{{ $isUrdu ? __('Advance Paid') : 'Advance Paid' }}</td>
                        <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#16a34a">Rs {{ number_format($order->advance_amount) }}</td>
                    </tr>
                    <tr class="row-balance">
                        <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#dc2626">{{ $isUrdu ? __('Balance Due (this order)') : 'Balance Due (this order)' }}</td>
                        <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#dc2626">Rs {{ number_format($order->balance_amount) }}</td>
                    </tr>
                    @if($showPreviousRow)
                    <tr class="row-prev">
                        <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#d97706">{{ $isUrdu ? __('Previous Dues') : 'Previous Dues' }}</td>
                        <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#d97706">Rs {{ number_format($previousBalance) }}</td>
                    </tr>
                    @endif
                    <tr class="row-grand">
                        <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#e2e8f0;font-weight:700">{{ $isUrdu ? __('Grand Total Owed') : 'Grand Total Owed' }}</td>
                        <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#fbbf24;font-weight:800;font-size:13px">Rs {{ number_format($grandTotal) }}</td>
                    </tr>
                </table>
            </div>
        @else
            <div style="display: table-cell; vertical-align: bottom; text-align: left; width: 50%;">
                <div style="display: inline-block; text-align: center;">
                    <img src="data:image/png;base64,{{ $trackingQrB64 }}" alt="Track order QR" style="width:100px;height:100px;display:block;margin:0 auto 4px;">
                    <br/>
                    <span style="font-size:7.5px;color:#94a3b8;font-family:DejaVu Sans,sans-serif">Scan to Track</span>
                </div>
            </div>
            <div class="payment-wrap-inner" style="display: table-cell; vertical-align: top; text-align: right; width: 50%;">
                <table class="payment-table" style="display: inline-table; width: 270px; border-collapse: collapse;">
                    <tr>
                        <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#64748b">{{ $isUrdu ? __('Total Amount') : 'Total Amount' }}</td>
                        <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#1e293b">Rs {{ number_format($order->total_amount) }}</td>
                    </tr>
                    <tr class="row-advance">
                        <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#16a34a">{{ $isUrdu ? __('Advance Paid') : 'Advance Paid' }}</td>
                        <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#16a34a">Rs {{ number_format($order->advance_amount) }}</td>
                    </tr>
                    <tr class="row-balance">
                        <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#dc2626">{{ $isUrdu ? __('Balance Due (this order)') : 'Balance Due (this order)' }}</td>
                        <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#dc2626">Rs {{ number_format($order->balance_amount) }}</td>
                    </tr>
                    @if($showPreviousRow)
                    <tr class="row-prev">
                        <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#d97706">{{ $isUrdu ? __('Previous Dues') : 'Previous Dues' }}</td>
                        <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#d97706">Rs {{ number_format($previousBalance) }}</td>
                    </tr>
                    @endif
                    <tr class="row-grand">
                        <td class="lbl" style="font-family:DejaVu Sans,sans-serif;color:#e2e8f0;font-weight:700">{{ $isUrdu ? __('Grand Total Owed') : 'Grand Total Owed' }}</td>
                        <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#fbbf24;font-weight:800;font-size:13px">Rs {{ number_format($grandTotal) }}</td>
                    </tr>
                </table>
            </div>
        @endif
    </div>
    @if($order->notes)
    <div class="note-box"><strong>{{ $isUrdu ? __('Note:') : 'Note:' }}</strong> {{ $order->notes }}</div>
    @endif

    @if(!empty($order->extras))
    <div class="note-box">
        <strong>{{ $isUrdu ? __('Extras / Add-ons') : 'Extras / Add-ons' }}:</strong>
        @foreach($order->extras as $extra)
        <span>{{ $extra['name'] }} — Rs {{ number_format($extra['price']) }}</span>@if(!$loop->last) &nbsp;|&nbsp; @endif
        @endforeach
    </div>
    @endif

    @if($styleOptions->isNotEmpty())
    <div class="note-box">
        <strong>{{ $isUrdu ? __('Style & Finishing Options') : 'Style &amp; Finishing Options' }}:</strong>
        {{ $styleOptions->map(fn ($value, $key) => ($styleMetaLabels[$key] ?? ucfirst(str_replace('_', ' ', $key))) . ': ' . $value)->implode(' | ') }}
    </div>
    @endif

    {{-- Handwritten notes placeholder --}}
    <div style="margin: 12px 0; border: 1px dashed #cbd5e1; border-radius: 4px; padding: 6px 10px; height: 50px;">
        <div style="font-size: 8.5px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 4px;">
            {{ $isUrdu ? 'اضافی نوٹ / ہدایات (دستخطی):' : 'Handwritten Notes / Instructions:' }}
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
            <br><span style="font-size:8.5px;color:#94a3b8">
                {{ $isUrdu ? __('Track order:') : 'Track order:' }}
                <a href="{{ $trackingUrl }}" style="color:#2563eb;text-decoration:none;font-size:7.5px">{{ $trackingUrl }}</a>
            </span>
        </div>
        <div class="footer-right" style="text-align:right">
            <div style="margin-bottom:3px;font-size:8.5px;color:#94a3b8">{{ $isUrdu ? __('Printed:') : 'Printed:' }} {{ now()->format('d M Y, h:i A') }}</div>
        </div>
    </div>

</div>
@endforeach

</body>
</html>
