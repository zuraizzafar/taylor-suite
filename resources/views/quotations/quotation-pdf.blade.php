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
    .totals-wrap-inner { text-align: left; }
    .footer-right { text-align: left; }
    .footer-left { text-align: right; }
    @endif

    .page { padding: 22px 28px 18px; font-family: DejaVu Sans, sans-serif; }

    .info-row, .totals-wrap, .note-box, table.items, .legal-box, .design-box {
        page-break-inside: avoid !important;
    }

    .badge-bar { margin-bottom: 8px; }
    .badge-bar span {
        display: inline-block;
        background: #f1f5f9;
        border: 1px solid #cbd5e1;
        font-size: 8px;
        font-weight: 700;
        text-transform: uppercase;
        letter-spacing: 1.5px;
        color: #64748b;
        padding: 2px 10px;
        border-radius: 12px;
    }

    .header {
        display: table;
        width: 100%;
        padding-bottom: 8px;
        border-bottom: 2px solid #1e293b;
        margin-bottom: 10px;
    }
    .header-left  { display: table-cell; vertical-align: top; width: 60%; }
    .header-right { display: table-cell; vertical-align: top; text-align: right; }

    .logo-fallback { font-size: 20px; font-weight: 800; color: #0f172a; letter-spacing: -0.5px; }
    .logo-img      { height: 60px; width: auto; }
    .company-meta  { font-size: 9.5px; color: #64748b; margin-top: 3px; line-height: 1.4; }

    .doc-title  { font-size: 22px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 1.5px; }
    .doc-no     { display: inline-block; background: #1e293b; color: #fff; font-size: 9.5px; font-weight: 700; letter-spacing: 0.5px; padding: 2px 8px; border-radius: 4px; margin-top: 3px; }
    .doc-dates  { font-size: 9px; color: #475569; margin-top: 3px; line-height: 1.5; }
    .doc-dates strong { color: #1e293b; }

    .info-row       { display: table; width: 100%; margin-bottom: 10px; border-spacing: 10px 0; }
    .info-cell      { display: table-cell; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 10px; vertical-align: top; }
    .info-cell-title { font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 3px; }
    .info-cell-name  { font-size: 11px; font-weight: 700; color: #0f172a; }
    .info-cell-sub   { font-size: 9px; color: #64748b; margin-top: 2px; line-height: 1.5; }

    table.items           { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 11px; }
    table.items thead tr  { background: #1e293b; }
    table.items thead th  { padding: 6px 8px; font-size: 12px; font-weight: 600; text-align: left; letter-spacing: 0.3px; color: #ffffff; background: #1e293b; }
    table.items tbody tr:nth-child(even) { background: #f8fafc; }
    table.items tbody td  { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; vertical-align: middle; font-size: 11px; color: #1e293b; }

    .totals-wrap       { display: table; width: 100%; margin-bottom: 10px; }
    .totals-wrap-inner { display: table-cell; text-align: right; }
    .totals-table      { width: 260px; border: 1px solid #e2e8f0; border-radius: 6px; border-collapse: collapse; display: inline-table; }
    .totals-table td   { padding: 5px 10px; font-size: 10px; }
    .totals-table tr   { border-bottom: 1px solid #f1f5f9; }
    .totals-table tr:last-child { border-bottom: none; }
    .lbl { color: #64748b; }
    .val { text-align: right; font-weight: 600; }
    .row-advance { background: #f0fdf4; }
    .row-balance { background: #1e293b; }

    .note-box   { margin-bottom: 8px; font-size: 9.5px; color: #64748b; padding: 5px 8px; background: #f8fafc; border-left: 3px solid #cbd5e1; border-radius: 3px; }
    .note-box strong { color: #1e293b; }

    .design-box   { border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; margin-bottom: 10px; background: #fafafa; }
    .design-title { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #1e293b; margin-bottom: 4px; }
    .design-text  { font-size: 9.5px; color: #475569; line-height: 1.5; }

    .legal-box   { border: 1px solid #e2e8f0; border-left: 4px solid #1e293b; border-radius: 4px; padding: 7px 10px; margin-bottom: 10px; background: #f8fafc; }
    .legal-title { font-size: 7.5px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 3px; }
    .legal-text  { font-size: 9px; color: #475569; line-height: 1.5; }

    .footer       { border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 8.5px; color: #94a3b8; display: table; width: 100%; }
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
    $companyWebsite = $settings['company_website']     ?? '';
    $logoPath       = $settings['logo_path']           ?? null;
    $isUrdu         = app()->getLocale() === 'ur';

    $validityNote = $isUrdu
        ? ($settings['quotation_validity_note_ur'] ?? 'یہ کوٹیشن مذکورہ بالا تاریخ سے صرف بتائی گئی مدت کے لیے کارآمد ہے۔ حتمی قیمت پیمائش اور حتمی ڈیزائن کی تصدیق کے بعد تبدیل ہو سکتی ہے۔')
        : ($settings['quotation_validity_note_en'] ?? 'This quotation is valid only for the period stated above from the quotation date. Final pricing may vary after measurements and design confirmation.');

    $logoB64  = null;
    $logoMime = 'image/png';
    if ($logoPath) {
        $fullPath = storage_path('app/public/' . $logoPath);
        if (file_exists($fullPath)) {
            $logoB64  = base64_encode(file_get_contents($fullPath));
            $logoMime = mime_content_type($fullPath);
        }
    }
@endphp

<div class="page">

    <div class="badge-bar"><span>{{ $isUrdu ? __('Customer Copy') : 'Customer Copy' }}</span></div>

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
                @if($companyWebsite) &nbsp;|&nbsp; {{ $companyWebsite }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">{{ $isUrdu ? __('Quotation') : 'Quotation' }}</div>
            <div><span class="doc-no">{{ $quotation->quotation_number }}</span></div>
            <div class="doc-dates">
                {{ $isUrdu ? __('Quotation Date') : 'Quotation Date' }}: <strong>{{ $quotation->quotation_date->format('d M Y') }}</strong><br>
                {{ $isUrdu ? __('Validity') : 'Validity' }}: <strong>{{ $quotation->validity_days }} {{ $isUrdu ? __('Days') : 'Days' }}</strong>
            </div>
        </div>
    </div>

    {{-- CUSTOMER --}}
    <div class="info-row">
        <div class="info-cell" style="width:96%">
            <div class="info-cell-title">{{ $isUrdu ? __('Quotation For') : 'Quotation For' }}</div>
            <div class="info-cell-name">{{ $quotation->customer->name }}</div>
            <div class="info-cell-sub">
                {{ $isUrdu ? __('Phone') : 'Phone' }}: {{ $quotation->customer->mobile }}
                @if($quotation->customer->address)<br>{{ $quotation->customer->address }}@endif
            </div>
        </div>
    </div>

    {{-- ITEMS TABLE --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:24px">#</th>
                <th>{{ $isUrdu ? __('Description') : 'Description' }}</th>
                <th style="width:60px;text-align:right">{{ $isUrdu ? __('Qty') : 'Qty' }}</th>
                <th style="width:100px;text-align:right">{{ $isUrdu ? __('Amount') : 'Amount' }}</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td style="text-align:right">{{ rtrim(rtrim(number_format((float) $item->qty, 2), '0'), '.') }}</td>
                <td style="text-align:right">Rs {{ number_format($item->line_total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    {{-- TOTALS --}}
    <div class="totals-wrap">
        <div class="totals-wrap-inner">
            <table class="totals-table">
                <tr>
                    <td class="lbl">{{ $isUrdu ? __('Total Quotation Amount') : 'Total Quotation Amount' }}</td>
                    <td class="val">Rs {{ number_format($quotation->total_amount) }}</td>
                </tr>
                <tr class="row-advance">
                    <td class="lbl" style="color:#16a34a">{{ $isUrdu ? __('Advance Required') : 'Advance Required' }} ({{ rtrim(rtrim(number_format((float) $quotation->advance_percentage, 2), '0'), '.') }}%)</td>
                    <td class="val" style="color:#16a34a">Rs {{ number_format($quotation->advance_amount) }}</td>
                </tr>
                <tr class="row-balance">
                    <td class="lbl" style="color:#e2e8f0;font-weight:700">{{ $isUrdu ? __('Remaining Balance') : 'Remaining Balance' }}</td>
                    <td class="val" style="color:#fbbf24;font-weight:800;font-size:11.5px">Rs {{ number_format($quotation->balance_amount) }}</td>
                </tr>
            </table>
        </div>
    </div>

    @if($quotation->design_reference)
    <div class="design-box">
        <div class="design-title">{{ $isUrdu ? __('Design & Embroidery Reference') : 'Design & Embroidery Reference' }}</div>
        <div class="design-text">{{ $quotation->design_reference }}</div>
    </div>
    @endif

    @if($quotation->notes)
    <div class="note-box"><strong>{{ $isUrdu ? __('Note:') : 'Note:' }}</strong> {{ $quotation->notes }}</div>
    @endif

    {{-- VALIDITY NOTICE --}}
    <div class="legal-box">
        <div class="legal-title">{{ $isUrdu ? __('Quotation Notice') : 'Quotation Notice' }}</div>
        <div class="legal-text">{{ $validityNote }}</div>
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

</body>
</html>
