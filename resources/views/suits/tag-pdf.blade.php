<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body {
        font-family: DejaVu Sans, sans-serif;
        font-size: 11px;
        color: #1e293b;
        width: 200px;
        height: 200px;
        display: flex;
        align-items: center;
        justify-content: center;
    }
    .tag {
        border: 2px solid #0f172a;
        border-radius: 6px;
        padding: 10px;
        width: 200px;
        text-align: center;
    }
    .shop { font-size: 9px; color: #64748b; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 4px; }
    .code { font-size: 18px; font-weight: bold; color: #2563eb; margin-bottom: 4px; font-family: 'Courier New', monospace; }
    .name { font-size: 12px; font-weight: bold; color: #1e293b; margin-bottom: 2px; }
    .fabric { font-size: 10px; color: #475569; margin-bottom: 6px; }
    .qr { margin: 4px auto; }
    .scan-text { font-size: 8px; color: #94a3b8; margin-top: 4px; }
    hr { border: none; border-top: 1px dashed #cbd5e1; margin: 6px 0; }
</style>
</head>
<body>
<div class="tag">
    <div class="shop">✂️ The Suit Tailor</div>
    <hr>
    <div class="code">{{ $suit->suit_code }}</div>
    <div class="name">{{ $suit->customer->name }}</div>
    <div class="fabric">Fabric: {{ $suit->fabric_meter }}m {{ $suit->fabric_description }}</div>
    <div class="fabric">{{ $suit->suit_type }}</div>
    <hr>
    @if($qrImage)
    <div class="qr">
        <img src="data:image/svg+xml;base64,{{ $qrImage }}" width="90" height="90" alt="QR">
    </div>
    <div class="scan-text">Scan to check status</div>
    @endif
</div>
</body>
</html>
