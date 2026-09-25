{{-- Shared style + page header for the tax/discount report PDFs. Params: $title, $from, $to, $settings --}}
@php
    $companyName    = $settings['company_name']    ?? 'The Suit Tailor';
    $companyTagline = $settings['company_tagline'] ?? 'Professional Tailoring Services';
    $ntn            = $settings['company_tax_number'] ?? '';
    $strn           = $settings['tax_registration_no'] ?? '';
    $logoPath       = $settings['logo_path'] ?? null;
    $logoB64 = null; $logoMime = 'image/png';
    if ($logoPath && file_exists(storage_path('app/public/' . $logoPath))) {
        $logoB64  = base64_encode(file_get_contents(storage_path('app/public/' . $logoPath)));
        $logoMime = mime_content_type(storage_path('app/public/' . $logoPath));
    }
@endphp
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; font-family: DejaVu Sans, sans-serif; }
    body { font-size: 10px; color: #1e293b; background: #fff; }
    .page { padding: 22px 26px 18px; }
    .header { display: table; width: 100%; padding-bottom: 8px; border-bottom: 2px solid #1e293b; margin-bottom: 12px; }
    .header-left { display: table-cell; vertical-align: top; width: 55%; }
    .header-right { display: table-cell; vertical-align: top; text-align: right; }
    .logo-img { height: 50px; width: auto; }
    .logo-fallback { font-size: 18px; font-weight: 700; color: #0f172a; }
    .company-meta { font-size: 8.5px; color: #64748b; margin-top: 3px; line-height: 1.4; }
    .report-title { font-size: 16px; font-weight: 700; color: #0f172a; text-transform: uppercase; }
    .report-meta { font-size: 9px; color: #475569; margin-top: 3px; }
    .kpi-row { display: table; width: 100%; margin-bottom: 12px; border-spacing: 6px 0; }
    .kpi-card { display: table-cell; background: #f8fafc; border: 1px solid #e2e8f0; border-radius: 6px; padding: 7px; text-align: center; }
    .kpi-lbl { font-size: 7px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 2px; }
    .kpi-val { font-size: 13px; font-weight: 700; color: #0f172a; }
    .kpi-sub { font-size: 7.5px; color: #94a3b8; margin-top: 2px; }
    h3.sec { font-size: 10px; font-weight: 700; text-transform: uppercase; letter-spacing: 0.6px; color: #475569; margin: 10px 0 4px; }
    table.report-table { width: 100%; border-collapse: collapse; margin-bottom: 10px; font-size: 9px; }
    table.report-table thead tr { background: #1e293b; }
    table.report-table thead th { padding: 5px 6px; text-align: left; font-weight: 700; color: #fff; background: #1e293b; }
    table.report-table tbody tr:nth-child(even) { background: #f8fafc; }
    table.report-table tbody td { padding: 4px 6px; border-bottom: 1px solid #f1f5f9; }
    table.report-table tfoot td { padding: 5px 6px; background: #f1f5f9; border-top: 1.5px solid #cbd5e1; font-weight: 700; }
    .r { text-align: right; }
    .footer { border-top: 1px solid #e2e8f0; padding-top: 6px; font-size: 8px; color: #94a3b8; margin-top: 8px; }
</style>
<div class="header">
    <div class="header-left">
        @if($logoB64)<img class="logo-img" src="data:{{ $logoMime }};base64,{{ $logoB64 }}" alt="">@else<div class="logo-fallback">{{ $companyName }}</div>@endif
        <div class="company-meta">
            {{ $companyName }} &mdash; {{ $companyTagline }}
            @if($ntn)<br>NTN: {{ $ntn }}@endif @if($strn) &nbsp;|&nbsp; {{ $settings['tax_label'] ?? 'GST' }} Reg. No.: {{ $strn }}@endif
        </div>
    </div>
    <div class="header-right">
        <div class="report-title">{{ $title }}</div>
        <div class="report-meta">Period: <strong>{{ \Carbon\Carbon::parse($from)->format('d M Y') }} &ndash; {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</strong></div>
    </div>
</div>
