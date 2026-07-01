<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: DejaVu Sans, sans-serif;
        background: #fff;
        color: #1e293b;
    }
    .tag {
        width: 210px;
        margin: 8px auto;
        border: 2px solid #1e293b;
        border-radius: 10px;
        padding: 14px 12px;
        text-align: center;
    }
    .tag-shop {
        font-size: 8px;
        font-weight: 700;
        letter-spacing: 2px;
        text-transform: uppercase;
        color: #94a3b8;
        margin-bottom: 8px;
    }
    .tag-roll {
        font-size: 18px;
        font-weight: 800;
        color: #2563eb;
        font-family: DejaVu Sans Mono, monospace;
        margin-bottom: 4px;
    }
    .tag-divider { border: none; border-top: 1px dashed #e2e8f0; margin: 8px 0; }
    .tag-name { font-size: 13px; font-weight: 700; color: #0f172a; margin-bottom: 4px; }
    .tag-meta { font-size: 9px; color: #64748b; line-height: 1.5; margin-bottom: 6px; }
    .tag-qr img { width: 100px; height: 100px; }
</style>
</head>
<body>
<div class="tag">
    <div class="tag-shop">{{ config('app.name', 'Suit Tailor') }}</div>
    <div class="tag-roll">{{ $fabric->roll_number }}</div>
    <hr class="tag-divider">
    <div class="tag-name">{{ $fabric->fabric_type }} — {{ $fabric->color }}</div>
    <div class="tag-meta">
        {{ $fabric->brand }} @if($fabric->design_code) · {{ $fabric->design_code }} @endif<br>
        Rs {{ number_format($fabric->sale_price) }} / meter
    </div>
    @if($qrImage)
    <hr class="tag-divider">
    <div class="tag-qr">
        <img src="data:image/svg+xml;base64,{{ $qrImage }}" alt="QR">
    </div>
    @endif
</div>
</body>
</html>
