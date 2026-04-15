<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $suit->suit_code }} – Suit Status</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: #f8fafc;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 20px;
        }
        .card {
            background: white;
            border-radius: 16px;
            padding: 32px;
            max-width: 420px;
            width: 100%;
            box-shadow: 0 4px 24px rgba(0,0,0,0.08);
            text-align: center;
        }
        .shop { color: #64748b; font-size: 13px; letter-spacing: 1px; text-transform: uppercase; margin-bottom: 8px; }
        .code { font-size: 28px; font-weight: 800; color: #2563eb; font-family: monospace; margin-bottom: 4px; }
        .name { font-size: 18px; font-weight: 600; color: #1e293b; margin-bottom: 4px; }
        .mobile { font-size: 14px; color: #64748b; margin-bottom: 20px; }
        hr { border: none; border-top: 1px solid #e2e8f0; margin: 20px 0; }
        .status-label { font-size: 12px; color: #94a3b8; margin-bottom: 8px; }
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 999px;
            font-size: 16px;
            font-weight: 700;
        }
        .status-pending   { background: #e2e8f0; color: #475569; }
        .status-cutting   { background: #fef9c3; color: #92400e; }
        .status-stitching { background: #dbeafe; color: #1e40af; }
        .status-ready     { background: #dcfce7; color: #166534; }
        .status-delivered { background: #ede9fe; color: #5b21b6; }

        .detail-row { display: flex; justify-content: space-between; font-size: 13px; padding: 6px 0; }
        .detail-label { color: #94a3b8; }
        .detail-value { font-weight: 500; color: #1e293b; }
        .footer { margin-top: 24px; font-size: 11px; color: #cbd5e1; }
    </style>
</head>
<body>
<div class="card">
    <div class="shop">✂️ The Suit Tailor</div>
    <div class="code">{{ $suit->suit_code }}</div>
    <div class="name">{{ $suit->customer->name }}</div>
    <div class="mobile">📞 {{ $suit->customer->mobile }}</div>

    <div class="status-label">Current Status</div>
    <span class="status-badge status-{{ $suit->status }}">
        @php
        $icons = ['pending' => '⏳', 'cutting' => '✂️', 'stitching' => '🪡', 'ready' => '✅', 'delivered' => '📦'];
        @endphp
        {{ $icons[$suit->status] ?? '' }} {{ ucfirst($suit->status) }}
    </span>

    <hr>

    <div class="detail-row"><span class="detail-label">Suit Type</span><span class="detail-value">{{ $suit->suit_type }}</span></div>
    <div class="detail-row"><span class="detail-label">Fabric</span><span class="detail-value">{{ $suit->fabric_meter }}m</span></div>
    @if($suit->worker)
    <div class="detail-row"><span class="detail-label">Worker</span><span class="detail-value">{{ $suit->worker->name }}</span></div>
    @endif
    @if($suit->delivered_at)
    <div class="detail-row"><span class="detail-label">Delivered</span><span class="detail-value">{{ $suit->delivered_at->format('d M Y') }}</span></div>
    @endif

    <div class="footer">Powered by The Suit Tailor Management System</div>
</div>
</body>
</html>
