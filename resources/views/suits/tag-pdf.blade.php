<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: DejaVu Sans, sans-serif;
        background: #fff;
        padding: 40px;
        color: #1e293b;
    }

    /* Centre a single tag card on the A4 page */
    .page-center {
        display: table;
        width: 100%;
        height: 680px;
    }
    .page-center-inner {
        display: table-cell;
        vertical-align: middle;
        text-align: center;
    }

    /* Tag card */
    .tag {
        display: inline-block;
        width: 240px;
        border: 2px solid #1e293b;
        border-radius: 10px;
        padding: 18px 16px;
        text-align: center;
        background: #fff;
    }

    .tag-shop {
        font-size: 8.5px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 10px;
    }

    .tag-code {
        font-size: 22px;
        font-weight: 800;
        color: #2563eb;
        letter-spacing: 1px;
        font-family: DejaVu Sans Mono, monospace;
        margin-bottom: 6px;
    }

    .tag-divider {
        border: none;
        border-top: 1px dashed #e2e8f0;
        margin: 10px 0;
    }

    .tag-name {
        font-size: 14px;
        font-weight: 700;
        color: #0f172a;
        margin-bottom: 4px;
    }

    .tag-meta {
        font-size: 9.5px;
        color: #64748b;
        line-height: 1.6;
        margin-bottom: 6px;
    }

    .tag-type {
        display: inline-block;
        background: #f1f5f9;
        border: 1px solid #e2e8f0;
        font-size: 9px;
        font-weight: 600;
        color: #475569;
        padding: 2px 10px;
        border-radius: 999px;
        margin-bottom: 12px;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .tag-qr { margin: 0 auto 6px; }
    .tag-qr img { width: 110px; height: 110px; }

    .tag-scan {
        font-size: 8px;
        color: #94a3b8;
        letter-spacing: 0.5px;
    }

    .tag-url {
        font-size: 7.5px;
        color: #cbd5e1;
        margin-top: 3px;
        word-break: break-all;
    }
</style>
</head>
<body>

@php
    $scanUrl = route('scan.show', $suit->suit_code);
@endphp

<div class="page-center">
    <div class="page-center-inner">
        <div class="tag">
            <div class="tag-shop">{{ config('app.name', 'Suit Tailor') }}</div>

            <div class="tag-code">{{ $suit->suit_code }}</div>

            <hr class="tag-divider">

            <div class="tag-name">{{ $suit->customer->name }}</div>
            <div class="tag-meta">
                File: {{ $suit->customer->file_number }}<br>
                Fabric: {{ $suit->fabric_meter }}m
                @if($suit->fabric_description) &mdash; {{ $suit->fabric_description }}@endif
                @if($suit->worker)<br>Worker: {{ $suit->worker->name }}@endif
            </div>
            <div class="tag-type">{{ $suit->suit_type }}</div>

            @if($qrImage)
            <hr class="tag-divider">
            <div class="tag-qr">
                <img src="data:image/svg+xml;base64,{{ $qrImage }}" alt="QR">
            </div>
            <div class="tag-scan">Scan to check suit status</div>
            <div class="tag-url">{{ $scanUrl }}</div>
            @endif
        </div>
    </div>
</div>

</body>
</html>

