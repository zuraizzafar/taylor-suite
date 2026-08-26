@php
    $companyName    = $settings['company_name']        ?? 'The Suit Tailor';
    $companyTagline = $settings['company_tagline']     ?? 'Professional Tailoring Services';
    $companyAddress = $settings['company_address']     ?? '';
    $companyPhone   = $settings['company_phone']       ?? '';
    $companyEmail   = $settings['company_email']       ?? '';
    $companyWebsite = $settings['company_website']     ?? '';
    $logoPath       = $settings['logo_path']           ?? null;

    $validityNote = $settings['quotation_validity_note_ur']
        ?? 'یہ کوٹیشن مذکورہ بالا تاریخ سے صرف بتائی گئی مدت کے لیے کارآمد ہے۔ حتمی قیمت پیمائش اور حتمی ڈیزائن کی تصدیق کے بعد تبدیل ہو سکتی ہے۔';

    $logoB64 = null; $logoMime = 'image/png';
    if ($logoPath) {
        $fullPath = storage_path('app/public/' . $logoPath);
        if (file_exists($fullPath)) {
            $logoB64 = base64_encode(file_get_contents($fullPath));
            $logoMime = mime_content_type($fullPath);
        }
    }
@endphp
<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>کوٹیشن {{ $quotation->quotation_number }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Noto Nastaliq Urdu', serif;
        font-size: 11.5px;
        line-height: 1.35;
        color: #1e293b;
        background: #f1f5f9;
        direction: rtl;
        text-align: right;
    }
    body * { line-height: 1.35; }

    .print-bar {
        position: fixed; top: 0; right: 0; left: 0; z-index: 100;
        background: #1e293b; color: #fff;
        padding: 8px 20px;
        display: flex; align-items: center; justify-content: space-between;
        font-family: 'Noto Nastaliq Urdu', serif;
    }
    .print-bar .title { font-size: 12px; font-weight: 600; }
    .print-bar button {
        background: #334155; color: #fff; border: none; cursor: pointer;
        padding: 4px 12px; border-radius: 6px; font-size: 12px;
        font-family: 'Noto Nastaliq Urdu', serif;
        margin-right: 8px;
    }
    .print-bar button:hover { background: #475569; }
    .print-bar .btn-print { background: #2563eb; }
    .print-bar .btn-print:hover { background: #1d4ed8; }

    .content { margin-top: 52px; padding: 12px 0 24px; }
    .page {
        max-width: 820px; margin: 0 auto 16px;
        background: #fff;
        padding: 20px 24px 16px;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,.1);
    }

    .badge-bar { margin-bottom: 8px; }
    .badge-bar span {
        display: inline-block;
        background: #f1f5f9; border: 1px solid #cbd5e1;
        font-size: 8.5px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1px; color: #64748b;
        padding: 2px 10px; border-radius: 20px;
    }

    .header {
        display: flex; justify-content: space-between; align-items: flex-start;
        padding-bottom: 8px; border-bottom: 2px solid #1e293b; margin-bottom: 10px;
    }
    .header-brand .logo-img { height: 48px; width: auto; }
    .header-brand .logo-fallback { font-size: 18px; font-weight: 800; color: #0f172a; }
    .header-brand .company-meta { font-size: 9.5px; color: #64748b; margin-top: 3px; line-height: 1.3; }
    .header-right { text-align: left; }
    .doc-title { font-size: 20px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 1px; font-family: sans-serif; }
    .doc-no {
        display: inline-block; background: #1e293b; color: #fff;
        font-size: 9.5px; font-weight: 700; padding: 2px 8px; border-radius: 4px; margin-top: 3px; font-family: monospace;
    }
    .doc-dates { font-size: 10px; color: #475569; margin-top: 4px; line-height: 1.3; }
    .doc-dates strong { color: #1e293b; }

    .info-row { display: flex; gap: 10px; margin-bottom: 10px; }
    .info-cell { flex: 1; border: 1px solid #e2e8f0; border-radius: 6px; padding: 6px 10px; }
    .info-cell-title { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 3px; font-family: sans-serif; }
    .info-cell-name { font-size: 11.5px; font-weight: 700; color: #0f172a; }
    .info-cell-sub { font-size: 9.5px; color: #64748b; margin-top: 2px; line-height: 1.3; }

    table.items { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    table.items thead tr { background: #1e293b; }
    table.items thead th { padding: 5px 8px; font-size: 11px; font-weight: 600; color: #fff; text-align: right; }
    table.items tbody tr:nth-child(even) { background: #f8fafc; }
    table.items tbody td { padding: 6px 8px; border-bottom: 1px solid #f1f5f9; font-size: 11px; }

    .totals-wrap { display: flex; justify-content: flex-start; margin-bottom: 10px; }
    .totals-table { min-width: 260px; border: 1px solid #e2e8f0; border-radius: 6px; border-collapse: collapse; }
    .totals-table td { padding: 5px 10px; font-size: 9.5px; }
    .totals-table tr { border-bottom: 1px solid #f1f5f9; }
    .totals-table tr:last-child { border-bottom: none; }
    .lbl { color: #64748b; }
    .val { text-align: left; font-weight: 600; }
    .row-advance td { background: #f0fdf4; }
    .row-balance td { background: #1e293b; }
    .row-balance .lbl { color: #e2e8f0; font-weight: 700; }
    .row-balance .val { color: #fbbf24; font-weight: 800; font-size: 11.5px; }

    .note-box { margin-bottom: 6px; font-size: 10px; color: #64748b; padding: 5px 8px; background: #f8fafc; border-right: 3px solid #cbd5e1; border-radius: 3px; }
    .note-box strong { color: #1e293b; }

    .design-box { border: 1px solid #e2e8f0; border-radius: 6px; padding: 8px 10px; margin-bottom: 10px; background: #fafafa; }
    .design-title { font-size: 10.5px; font-weight: 700; text-transform: uppercase; color: #1e293b; margin-bottom: 4px; font-family: sans-serif; }
    .design-text { font-size: 10px; color: #475569; line-height: 1.4; }

    .legal-box { border: 1px solid #e2e8f0; border-right: 4px solid #1e293b; border-radius: 4px; padding: 6px 10px; margin-bottom: 10px; background: #f8fafc; }
    .legal-title { font-size: 8px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.5px; color: #94a3b8; margin-bottom: 2px; font-family: sans-serif; }
    .legal-text { font-size: 9.5px; color: #475569; line-height: 1.3; }

    .footer { border-top: 1px solid #e2e8f0; padding-top: 6px; display: flex; justify-content: space-between; font-size: 8.5px; color: #94a3b8; }

    @media print {
        body { background: #fff; font-size: 10.5px; }
        .print-bar { display: none !important; }
        .content { margin-top: 0; padding: 0; }
        .page { box-shadow: none; border-radius: 0; margin: 0; padding: 10px 15px; max-width: 100%; }
        .info-row, .totals-wrap, .note-box, .design-box, .legal-box, table.items {
            page-break-inside: avoid !important;
        }
    }
</style>
</head>
<body>

<div class="print-bar">
    <div class="title">کوٹیشن نمبر: {{ $quotation->quotation_number }}</div>
    <div>
        <button class="btn-print" onclick="window.print()">🖨 پرنٹ کریں</button>
        <button onclick="window.history.back()">← واپس</button>
    </div>
</div>

<div class="content">
<div class="page">

    <div class="badge-bar"><span style="letter-spacing:0;font-family:'Noto Nastaliq Urdu',serif">گاہک کی نقل</span></div>

    <div class="header">
        <div class="header-brand">
            @if($logoB64)
            <img class="logo-img" src="data:{{ $logoMime }};base64,{{ $logoB64 }}" alt="{{ $companyName }}">
            @else
            <div class="logo-fallback">{{ $companyName }}</div>
            @endif
            <div class="company-meta">
                {{ $companyTagline }}
                @if($companyAddress) | {{ $companyAddress }}@endif
                @if($companyPhone) | {{ $companyPhone }}@endif
                @if($companyEmail) | {{ $companyEmail }}@endif
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title">QUOTATION</div>
            <div><span class="doc-no">{{ $quotation->quotation_number }}</span></div>
            <div class="doc-dates">
                تاریخ کوٹیشن: <strong>{{ $quotation->quotation_date->format('d M Y') }}</strong><br>
                میعاد: <strong>{{ $quotation->validity_days }} دن</strong>
            </div>
        </div>
    </div>

    <div class="info-row">
        <div class="info-cell" style="flex: 1;">
            <div class="info-cell-title">QUOTATION FOR</div>
            <div class="info-cell-name">{{ $quotation->customer->name }}</div>
            <div class="info-cell-sub">
                فون: {{ $quotation->customer->mobile }}
                @if($quotation->customer->address)<br>{{ $quotation->customer->address }}@endif
            </div>
        </div>
    </div>

    <table class="items">
        <thead>
            <tr>
                <th style="width:28px">#</th>
                <th>تفصیل</th>
                <th style="width:70px">تعداد</th>
                <th style="width:100px">رقم</th>
            </tr>
        </thead>
        <tbody>
            @foreach($quotation->items as $i => $item)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td>{{ $item->description }}</td>
                <td>{{ rtrim(rtrim(number_format((float) $item->qty, 2), '0'), '.') }}</td>
                <td>Rs {{ number_format($item->line_total) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="totals-wrap">
        <table class="totals-table">
            <tr>
                <td class="lbl">کل رقم</td>
                <td class="val">Rs {{ number_format($quotation->total_amount) }}</td>
            </tr>
            <tr class="row-advance">
                <td class="lbl" style="color:#16a34a">پیشگی ادائیگی ({{ rtrim(rtrim(number_format((float) $quotation->advance_percentage, 2), '0'), '.') }}%)</td>
                <td class="val" style="color:#16a34a">Rs {{ number_format($quotation->advance_amount) }}</td>
            </tr>
            <tr class="row-balance">
                <td class="lbl">باقی رقم</td>
                <td class="val">Rs {{ number_format($quotation->balance_amount) }}</td>
            </tr>
        </table>
    </div>

    @if($quotation->design_reference)
    <div class="design-box">
        <div class="design-title">ڈیزائن اور کڑھائی کا حوالہ</div>
        <div class="design-text">{{ $quotation->design_reference }}</div>
    </div>
    @endif

    @if($quotation->notes)
    <div class="note-box"><strong>نوٹ:</strong> {{ $quotation->notes }}</div>
    @endif

    <div class="legal-box">
        <div class="legal-title">QUOTATION NOTICE</div>
        <div class="legal-text">{{ $validityNote }}</div>
    </div>

    <div class="footer">
        <div>{{ $companyName }} — {{ $companyTagline }}</div>
        <div>پرنٹ: {{ now()->format('d M Y, h:i A') }}</div>
    </div>

</div>
</div>

<script>
document.fonts.ready.then(function() {
    setTimeout(function() { window.print(); }, 400);
});
</script>
</body>
</html>
