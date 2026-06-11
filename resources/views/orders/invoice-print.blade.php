@php
    $measurement = $order->customer->measurements->first()
                   ?? $order->suits->pluck('measurement')->filter()->first();

    $qFields = [
        'q_length'       => 'لمبائی',
        'q_shoulder'     => 'کندھا',
        'q_chest'        => 'سینہ',
        'q_waist'        => 'کمر',
        'q_seat'         => 'چوڑائی',
        'q_sleeve'       => 'آستین',
        'q_sleeve_width' => 'آستین چوڑائی',
        'q_collar'       => 'کالر',
        'q_front'        => 'آگے',
        'q_back'         => 'پیچھے',
        'q_armhole'      => 'بغل',
        'q_cuff'         => 'کف',
    ];
    $sFields = [
        's_length' => 'لمبائی',
        's_waist'  => 'کمر',
        's_seat'   => 'چوڑائی',
        's_thigh'  => 'ران',
        's_knee'   => 'گھٹنہ',
        's_bottom' => 'پانچہ',
        's_crotch' => 'کروچ',
        's_ankle'  => 'ٹخنہ',
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
    $legalNote      = $settings['invoice_legal_note']
                        ?? 'ادائیگی صرف اس انوائس پر درج مجاز بینک اکاؤنٹ کے ذریعے قبول کی جاتی ہے۔ کسی اور اکاؤنٹ پر کی گئی ادائیگی کی ذمہ داری دکان یا کمپنی پر نہیں ہوگی۔';

    $grandTotal      = $order->balance_amount + $previousBalance;
    $showPreviousRow = $previousBalance > 0;
    $trackingUrl     = route('tracking.show', ['tracking' => $order->order_number]);

    $styleMetaLabels = [
        'collar_style'     => 'گلا / کالر',
        'button_type'      => 'بٹن کی قسم',
        'button_count'     => 'بٹن کی تعداد',
        'ghera_style'      => 'گھیرا',
        'stitching_style'  => 'سلائی',
        'chak_patti'       => 'چاک پٹی',
        'kaj_hale'         => 'کاج / حلے',
        'pahuncha_style'   => 'پہنچہ',
        'front_patti_size' => 'اگلی پٹی',
        'design_number'    => 'ڈیزائن نمبر',
    ];
    $styleOptions = collect($measurement?->meta ?? [])->filter(fn ($value) => filled($value));

    $logoB64 = null; $logoMime = 'image/png';
    if ($logoPath) {
        $fullPath = storage_path('app/public/' . $logoPath);
        if (file_exists($fullPath)) {
            $logoB64 = base64_encode(file_get_contents($fullPath));
            $logoMime = mime_content_type($fullPath);
        }
    }
    $payQrB64 = null; $payQrMime = 'image/png';
    if ($paymentQrPath) {
        $qrFull = storage_path('app/public/' . $paymentQrPath);
        if (file_exists($qrFull)) {
            $payQrB64 = base64_encode(file_get_contents($qrFull));
            $payQrMime = mime_content_type($qrFull);
        }
    }

    $copies = [
        ['label' => 'کاپی (گاہک)', 'show_worker' => false],
        ['label' => 'کاپی (دکان)', 'show_worker' => true],
    ];
@endphp
<!DOCTYPE html>
<html lang="ur" dir="rtl">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<title>رسید {{ $order->order_number }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Noto+Nastaliq+Urdu:wght@400;500;600;700&display=swap" rel="stylesheet">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }

    body {
        font-family: 'Noto Nastaliq Urdu', serif;
        font-size: 14px;
        color: #1e293b;
        background: #f1f5f9;
        direction: rtl;
        text-align: right;
    }

    /* ── Print toolbar (screen only) ─────────────────── */
    .print-bar {
        position: fixed; top: 0; right: 0; left: 0; z-index: 100;
        background: #1e293b; color: #fff;
        padding: 8px 20px;
        display: flex; align-items: center; justify-content: space-between;
        font-family: 'Noto Nastaliq Urdu', serif;
    }
    .print-bar .title { font-size: 13px; font-weight: 600; }
    .print-bar button {
        background: #334155; color: #fff; border: none; cursor: pointer;
        padding: 6px 16px; border-radius: 6px; font-size: 13px;
        font-family: 'Noto Nastaliq Urdu', serif;
        margin-right: 8px;
    }
    .print-bar button:hover { background: #475569; }
    .print-bar .btn-print { background: #2563eb; }
    .print-bar .btn-print:hover { background: #1d4ed8; }

    /* ── Page wrapper ─────────────────────────────────── */
    .content { margin-top: 52px; padding: 20px 0 40px; }
    .page {
        max-width: 820px; margin: 0 auto 28px;
        background: #fff;
        padding: 36px 40px 28px;
        border-radius: 8px;
        box-shadow: 0 1px 4px rgba(0,0,0,.1);
    }

    /* ── Copy label ───────────────────────────────────── */
    .copy-label-bar { margin-bottom: 16px; }
    .copy-label-bar span {
        display: inline-block;
        background: #f1f5f9; border: 1px solid #cbd5e1;
        font-size: 10px; font-weight: 700; text-transform: uppercase;
        letter-spacing: 1.5px; color: #64748b;
        padding: 3px 14px; border-radius: 20px;
    }

    /* ── Header ───────────────────────────────────────── */
    .header {
        display: flex; justify-content: space-between; align-items: flex-start;
        padding-bottom: 14px; border-bottom: 2px solid #1e293b; margin-bottom: 18px;
    }
    .header-brand .logo-img { height: 70px; width: auto; }
    .header-brand .logo-fallback { font-size: 22px; font-weight: 800; color: #0f172a; }
    .header-brand .company-meta { font-size: 11px; color: #64748b; margin-top: 5px; line-height: 1.7; }
    .header-right { text-align: left; }
    .invoice-title { font-size: 28px; font-weight: 800; color: #0f172a; text-transform: uppercase; letter-spacing: 2px; font-family: sans-serif; }
    .invoice-no {
        display: inline-block; background: #1e293b; color: #fff;
        font-size: 11px; font-weight: 700; padding: 3px 10px; border-radius: 4px; margin-top: 5px; font-family: monospace;
    }
    .invoice-dates { font-size: 12px; color: #475569; margin-top: 6px; line-height: 2; }
    .invoice-dates strong { color: #1e293b; }

    /* ── Info boxes ───────────────────────────────────── */
    .info-row { display: flex; gap: 14px; margin-bottom: 18px; }
    .info-cell { flex: 1; border: 1px solid #e2e8f0; border-radius: 6px; padding: 10px 14px; }
    .info-cell-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 5px; font-family: sans-serif; }
    .info-cell-name { font-size: 14px; font-weight: 700; color: #0f172a; }
    .info-cell-sub { font-size: 11px; color: #64748b; margin-top: 4px; line-height: 1.8; }

    /* ── Suits table ──────────────────────────────────── */
    table.items { width: 100%; border-collapse: collapse; margin-bottom: 18px; }
    table.items thead tr { background: #1e293b; }
    table.items thead th { padding: 9px 10px; font-size: 13px; font-weight: 600; color: #fff; text-align: right; }
    table.items tbody tr:nth-child(even) { background: #f8fafc; }
    table.items tbody td { padding: 8px 10px; border-bottom: 1px solid #f1f5f9; font-size: 13px; }
    table.items tfoot td { padding: 7px 10px; background: #f1f5f9; border-top: 1.5px solid #cbd5e1; font-weight: 600; font-size: 12px; }

    /* Status */
    .st-pending   { color: #6b7280; }
    .st-cutting   { color: #d97706; }
    .st-stitching { color: #2563eb; }
    .st-ready     { color: #16a34a; }
    .st-delivered { color: #0f172a; }

    /* ── Measurements ─────────────────────────────────── */
    .meas-wrap { border: 1px solid #e2e8f0; border-radius: 6px; margin-bottom: 16px; overflow: hidden; }
    .meas-title-row { background: #f1f5f9; padding: 6px 12px; font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.8px; color: #475569; font-family: sans-serif; }
    .meas-section-hdr { font-size: 9px; font-weight: 700; text-transform: uppercase; color: #94a3b8; padding: 5px 12px 3px; background: #fff; border-top: 1px solid #f1f5f9; font-family: sans-serif; }
    table.meas-grid { width: 100%; border-collapse: collapse; }
    table.meas-grid td { padding: 4px 8px 5px; font-size: 11px; border-right: 1px solid #f1f5f9; border-bottom: 1px solid #f1f5f9; }
    table.meas-grid td:first-child { border-right: none; }
    .meas-lbl { color: #94a3b8; font-size: 10px; display: block; margin-bottom: 1px; }
    .meas-val { font-weight: 700; color: #0f172a; font-size: 12px; }
    .meas-empty { color: #cbd5e1; }

    /* ── Payment summary ──────────────────────────────── */
    .payment-wrap { display: flex; justify-content: flex-start; margin-bottom: 16px; }
    .payment-table { min-width: 280px; border: 1px solid #e2e8f0; border-radius: 6px; border-collapse: collapse; }
    .payment-table td { padding: 7px 14px; font-size: 13px; }
    .payment-table tr { border-bottom: 1px solid #f1f5f9; }
    .payment-table tr:last-child { border-bottom: none; }
    .lbl { color: #64748b; }
    .val { text-align: left; font-weight: 600; }
    .row-advance td { background: #f0fdf4; }
    .row-grand td { background: #1e293b; }
    .row-grand .lbl { color: #e2e8f0; font-weight: 700; }
    .row-grand .val { color: #fbbf24; font-weight: 800; font-size: 15px; }

    /* ── Notes / extras / legal ───────────────────────── */
    .note-box { margin-bottom: 12px; font-size: 12px; color: #64748b; padding: 8px 12px; background: #f8fafc; border-right: 3px solid #cbd5e1; border-radius: 3px; }
    .note-box strong { color: #1e293b; }
    .legal-box { border: 1px solid #e2e8f0; border-right: 4px solid #1e293b; border-radius: 4px; padding: 10px 14px; margin-bottom: 20px; background: #f8fafc; }
    .legal-title { font-size: 9px; font-weight: 700; text-transform: uppercase; letter-spacing: 1px; color: #94a3b8; margin-bottom: 4px; font-family: sans-serif; }
    .legal-text { font-size: 12px; color: #475569; line-height: 1.9; }

    /* ── Footer ───────────────────────────────────────── */
    .footer { border-top: 1px solid #e2e8f0; padding-top: 8px; display: flex; justify-content: space-between; font-size: 10px; color: #94a3b8; }
    .footer a { color: #2563eb; text-decoration: none; }

    /* ── Print overrides ──────────────────────────────── */
    @media print {
        body { background: #fff; font-size: 13px; }
        .print-bar { display: none !important; }
        .content { margin-top: 0; padding: 0; }
        .page { box-shadow: none; border-radius: 0; margin: 0; padding: 20px 24px; page-break-after: always; max-width: 100%; }
        .page:last-child { page-break-after: auto; }
    }
</style>
</head>
<body>

{{-- Print toolbar --}}
<div class="print-bar">
    <div class="title">رسید نمبر: {{ $order->order_number }}</div>
    <div>
        <button class="btn-print" onclick="window.print()">🖨 پرنٹ کریں</button>
        <button onclick="window.history.back()">← واپس</button>
    </div>
</div>

<div class="content">

@php $isUrdu = true; @endphp

@foreach($copies as $copy)
<div class="page">

    {{-- Copy label --}}
    <div class="copy-label-bar"><span style="letter-spacing:0;font-family:'Noto Nastaliq Urdu',serif">{{ $copy['label'] }}</span></div>

    {{-- HEADER --}}
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
            <div class="invoice-title">INVOICE</div>
            <div><span class="invoice-no">{{ $order->order_number }}</span></div>
            <div class="invoice-dates">
                تاریخ آرڈر: <strong>{{ $order->order_date->format('d M Y') }}</strong><br>
                تاریخ ترسیل: <strong>{{ $order->delivery_date?->format('d M Y') ?? '—' }}</strong><br>
                سوٹ: <strong>{{ $order->suits->count() }}</strong>
            </div>
        </div>
    </div>

    {{-- CUSTOMER + BANK --}}
    <div class="info-row">
        <div class="info-cell">
            <div class="info-cell-title">BILLED TO</div>
            <div class="info-cell-name">{{ $order->customer->name }}</div>
            <div class="info-cell-sub">
                فائل نمبر: <strong>{{ $order->customer->file_number }}</strong><br>
                موبائل: {{ $order->customer->mobile }}
                @if($order->customer->address)<br>{{ $order->customer->address }}@endif
            </div>
        </div>
        @if($bankName || $bankAccount)
        <div class="info-cell">
            <div class="info-cell-title">BANK DETAILS</div>
            @if($bankName)<div class="info-cell-name" style="font-size:13px">{{ $bankName }}</div>@endif
            <div class="info-cell-sub">
                @if($bankTitle)Title: <strong>{{ $bankTitle }}</strong><br>@endif
                @if($bankAccount)Account: <strong>{{ $bankAccount }}</strong>@endif
            </div>
            @if($payQrB64)
            <div style="margin-top:8px">
                <img src="data:{{ $payQrMime }};base64,{{ $payQrB64 }}" alt="QR" style="width:80px;height:80px">
                <div style="font-size:10px;color:#94a3b8;margin-top:3px">اسکین کر کے ادائیگی کریں</div>
            </div>
            @endif
        </div>
        @endif
    </div>

    {{-- SUITS TABLE --}}
    <table class="items">
        <thead>
            <tr>
                <th style="width:28px">#</th>
                <th>کوڈ</th>
                <th>قسم</th>
                <th>کپڑا</th>
                @if($copy['show_worker'])<th>کاریگر</th>@endif
                <th style="width:80px">حالت</th>
            </tr>
        </thead>
        <tbody>
            @foreach($order->suits as $i => $suit)
            <tr>
                <td>{{ $i + 1 }}</td>
                <td><strong>{{ $suit->suit_code }}</strong></td>
                <td>{{ $suit->suit_type }}</td>
                <td>
                    {{ $suit->fabric_meter ? $suit->fabric_meter . 'm' : '—' }}
                    @if($suit->fabric_description)
                    <br><small style="color:#94a3b8">{{ $suit->fabric_description }}</small>
                    @endif
                </td>
                @if($copy['show_worker'])<td>{{ $suit->worker?->name ?? '—' }}</td>@endif
                <td class="st-{{ $suit->status }}"><strong>{{ ucfirst($suit->status) }}</strong></td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="{{ $copy['show_worker'] ? 5 : 4 }}" style="text-align:right">کل سوٹ:</td>
                <td>{{ $order->suits->count() }}</td>
            </tr>
        </tfoot>
    </table>

    {{-- MEASUREMENTS --}}
    @php
        $hasMeas = $measurement
            && collect($qFields)->keys()->merge(collect($sFields)->keys())
                ->some(fn($k) => !empty($measurement->$k));
        $measurementNotes = $measurement?->notes;
    @endphp
    @if($hasMeas)
    <div class="meas-wrap">
        <div class="meas-title-row">
            پیمائش
            @if($measurementNotes)&nbsp;·&nbsp; <span style="font-weight:400;color:#64748b;text-transform:none;font-family:'Noto Nastaliq Urdu',serif">{{ $measurementNotes }}</span>@endif
        </div>
        <div class="meas-section-hdr">قمیض / کمیز</div>
        <table class="meas-grid">
            <tr>
                @foreach($qFields as $field => $label)
                <td style="width:8.33%">
                    <span class="meas-lbl">{{ $label }}</span>
                    @if(!empty($measurement->$field))
                    <span class="meas-val">{{ $measurement->$field }}</span>
                    @else
                    <span class="meas-val meas-empty">—</span>
                    @endif
                </td>
                @endforeach
            </tr>
        </table>
        <div class="meas-section-hdr">شلوار / تراؤزر</div>
        <table class="meas-grid">
            <tr>
                @foreach($sFields as $field => $label)
                <td style="width:12.5%">
                    <span class="meas-lbl">{{ $label }}</span>
                    @if(!empty($measurement->$field))
                    <span class="meas-val">{{ $measurement->$field }}</span>
                    @else
                    <span class="meas-val meas-empty">—</span>
                    @endif
                </td>
                @endforeach
            </tr>
        </table>
    </div>
    @endif

    {{-- PAYMENT SUMMARY --}}
    <div class="payment-wrap">
        <table class="payment-table">
            <tr>
                <td class="lbl">کل رقم</td>
                <td class="val">Rs {{ number_format($order->total_amount) }}</td>
            </tr>
            <tr class="row-advance">
                <td class="lbl" style="color:#16a34a">پیشگی ادائیگی</td>
                <td class="val" style="color:#16a34a">Rs {{ number_format($order->advance_amount) }}</td>
            </tr>
            <tr>
                <td class="lbl" style="color:#dc2626">باقی رقم</td>
                <td class="val" style="color:#dc2626">Rs {{ number_format($order->balance_amount) }}</td>
            </tr>
            @if($showPreviousRow)
            <tr>
                <td class="lbl" style="color:#d97706">پرانا بقایا</td>
                <td class="val" style="color:#d97706">Rs {{ number_format($previousBalance) }}</td>
            </tr>
            @endif
            <tr class="row-grand">
                <td class="lbl">کل واجب الادا</td>
                <td class="val">Rs {{ number_format($grandTotal) }}</td>
            </tr>
        </table>
    </div>

    @if($order->notes)
    <div class="note-box"><strong>نوٹ:</strong> {{ $order->notes }}</div>
    @endif

    @if(!empty($order->extras))
    <div class="note-box">
        <strong>اضافی کام:</strong>
        @foreach($order->extras as $extra)
        {{ $extra['name'] }} — Rs {{ number_format($extra['price']) }}@if(!$loop->last) &nbsp;|&nbsp; @endif
        @endforeach
    </div>
    @endif

    @if($styleOptions->isNotEmpty())
    <div class="note-box">
        <strong>اسٹائل:</strong>
        {{ $styleOptions->map(fn ($v, $k) => ($styleMetaLabels[$k] ?? $k) . ': ' . $v)->implode(' | ') }}
    </div>
    @endif

    <div class="legal-box">
        <div class="legal-title">PAYMENT NOTICE</div>
        <div class="legal-text">{{ $legalNote }}</div>
    </div>

    <div class="footer">
        <div>{{ $companyName }} — {{ $companyTagline }}</div>
        <div>ٹریک کریں: <a href="{{ $trackingUrl }}">{{ $trackingUrl }}</a> &nbsp;|&nbsp; پرنٹ: {{ now()->format('d M Y, h:i A') }}</div>
    </div>

</div>
@endforeach

</div>{{-- .content --}}

<script>
// Auto-print after fonts load (800ms grace period)
document.fonts.ready.then(function() {
    setTimeout(function() { window.print(); }, 400);
});
</script>
</body>
</html>
