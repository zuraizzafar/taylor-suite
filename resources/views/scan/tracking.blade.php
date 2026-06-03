<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $order ? $order->order_number . ' - Order Tracking' : 'Track Your Order' }}</title>
    <link rel="icon" type="image/svg+xml" href="/favicon.svg">
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(180deg, #eff6ff 0%, #f8fafc 45%, #ffffff 100%);
            min-height: 100vh;
            color: #0f172a;
            padding: 28px 16px 40px;
        }
        .wrap { max-width: 960px; margin: 0 auto; }
        .hero {
            background: white;
            border: 1px solid #dbeafe;
            border-radius: 24px;
            padding: 28px;
            box-shadow: 0 18px 40px rgba(37, 99, 235, 0.08);
            margin-bottom: 18px;
        }
        .eyebrow { color: #2563eb; font-size: 12px; font-weight: 700; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 10px; }
        h1 { font-size: 28px; line-height: 1.1; margin-bottom: 8px; }
        .sub { color: #64748b; font-size: 15px; line-height: 1.5; max-width: 640px; }
        .search {
            margin-top: 18px;
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }
        .search input {
            flex: 1 1 280px;
            border: 1px solid #cbd5e1;
            border-radius: 14px;
            padding: 13px 14px;
            font-size: 14px;
            outline: none;
        }
        .search input:focus { border-color: #2563eb; box-shadow: 0 0 0 3px rgba(37, 99, 235, 0.12); }
        .search button {
            border: 0;
            border-radius: 14px;
            background: #2563eb;
            color: white;
            font-size: 14px;
            font-weight: 700;
            padding: 13px 18px;
            cursor: pointer;
        }
        .search button:hover { background: #1d4ed8; }
        .hint { color: #64748b; font-size: 12px; margin-top: 10px; }
        .alert {
            background: #fef2f2;
            border: 1px solid #fecaca;
            color: #b91c1c;
            border-radius: 16px;
            padding: 14px 16px;
            margin-bottom: 16px;
            font-size: 14px;
        }
        .card {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 24px;
            box-shadow: 0 14px 30px rgba(15, 23, 42, 0.06);
            overflow: hidden;
        }
        .card-head {
            padding: 24px 24px 18px;
            border-bottom: 1px solid #e2e8f0;
            display: flex;
            justify-content: space-between;
            gap: 16px;
            flex-wrap: wrap;
        }
        .track-number { font-size: 13px; color: #2563eb; font-weight: 800; letter-spacing: 0.08em; text-transform: uppercase; margin-bottom: 8px; }
        .customer { font-size: 24px; font-weight: 800; margin-bottom: 4px; }
        .meta { font-size: 14px; color: #64748b; line-height: 1.6; }
        .summary {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 12px;
            padding: 18px 24px;
            background: #f8fafc;
            border-bottom: 1px solid #e2e8f0;
        }
        .summary-item {
            background: white;
            border: 1px solid #e2e8f0;
            border-radius: 16px;
            padding: 14px;
        }
        .summary-label { font-size: 11px; text-transform: uppercase; letter-spacing: 0.08em; color: #94a3b8; margin-bottom: 6px; }
        .summary-value { font-size: 18px; font-weight: 800; }
        .suits { padding: 24px; }
        .suits h2 { font-size: 18px; margin-bottom: 14px; }
        table { width: 100%; border-collapse: collapse; }
        th, td { text-align: left; padding: 12px 10px; border-bottom: 1px solid #e2e8f0; font-size: 14px; vertical-align: top; }
        th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.08em; color: #64748b; }
        .code { font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, monospace; font-weight: 700; color: #1d4ed8; }
        .status {
            display: inline-block;
            padding: 6px 10px;
            border-radius: 999px;
            font-size: 12px;
            font-weight: 700;
        }
        .status-pending { background: #e2e8f0; color: #475569; }
        .status-cutting { background: #fef3c7; color: #92400e; }
        .status-stitching { background: #dbeafe; color: #1d4ed8; }
        .status-ready { background: #dcfce7; color: #166534; }
        .status-delivered { background: #ede9fe; color: #6d28d9; }
        .footer-note { color: #94a3b8; font-size: 12px; text-align: center; margin-top: 16px; }
        @media (max-width: 720px) {
            body { padding: 16px 10px 28px; }
            .hero, .card-head, .summary, .suits { padding-left: 16px; padding-right: 16px; }
            h1 { font-size: 24px; }
            table, thead, tbody, th, td, tr { display: block; }
            thead { display: none; }
            tr {
                border: 1px solid #e2e8f0;
                border-radius: 16px;
                padding: 8px 10px;
                margin-bottom: 12px;
            }
            td {
                border-bottom: 0;
                padding: 6px 0;
            }
            td::before {
                content: attr(data-label);
                display: block;
                font-size: 11px;
                text-transform: uppercase;
                letter-spacing: 0.08em;
                color: #94a3b8;
                margin-bottom: 2px;
            }
        }
    </style>
</head>
<body>
<div class="wrap">
    <div class="hero">
        <div class="eyebrow">Order Tracking</div>
        <h1>Track Your Order</h1>
        <p class="sub">Enter your order tracking number or a suit code to see the current order status and all suits included in that order.</p>

        <form method="GET" action="{{ route('tracking.index') }}" class="search">
            <input type="text" name="q" value="{{ $tracking }}" placeholder="Enter order number or suit code" autocomplete="off">
            <button type="submit">Search Status</button>
        </form>
        <div class="hint">Examples: {{ $order?->order_number ?? 'ORD-2026-001' }} or {{ $order?->suits->first()?->suit_code ?? 'ST-001-1' }}</div>
    </div>

    @if($searchError)
    <div class="alert">{{ $searchError }}</div>
    @endif

    @if($order)
    <div class="card">
        <div class="card-head">
            <div>
                <div class="track-number">Tracking Number: {{ $order->order_number }}</div>
                <div class="customer">{{ $order->customer->name }}</div>
                <div class="meta">
                    Mobile: {{ $order->customer->mobile }}<br>
                    @if($order->customer->address)
                    {{ $order->customer->address }}<br>
                    @endif
                    Order Date: {{ $order->order_date?->format('d M Y') }}<br>
                    Delivery Date: {{ $order->delivery_date?->format('d M Y') }}
                </div>
            </div>
            <div class="meta">
                Total Amount: <strong>Rs {{ number_format($order->total_amount) }}</strong><br>
                Paid: <strong>Rs {{ number_format($order->advance_amount) }}</strong><br>
                Balance: <strong>Rs {{ number_format($order->balance_amount) }}</strong>
            </div>
        </div>

        <div class="summary">
            <div class="summary-item">
                <div class="summary-label">Total Suits</div>
                <div class="summary-value">{{ $order->suits->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Delivered</div>
                <div class="summary-value">{{ $order->suits->where('status', 'delivered')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">Ready</div>
                <div class="summary-value">{{ $order->suits->where('status', 'ready')->count() }}</div>
            </div>
            <div class="summary-item">
                <div class="summary-label">In Progress</div>
                <div class="summary-value">{{ $order->suits->whereIn('status', ['pending', 'cutting', 'stitching'])->count() }}</div>
            </div>
        </div>

        <div class="suits">
            <h2>Suits In This Order</h2>
            <table>
                <thead>
                    <tr>
                        <th>Suit Code</th>
                        <th>Type</th>
                        <th>Fabric</th>
                        <th>Worker</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->suits as $suit)
                    <tr>
                        <td data-label="Suit Code"><span class="code">{{ $suit->suit_code }}</span></td>
                        <td data-label="Type">{{ $suit->suit_type }}</td>
                        <td data-label="Fabric">
                            {{ $suit->fabric_meter }} meter
                            @if($suit->fabric_description)
                            , {{ $suit->fabric_description }}
                            @endif
                        </td>
                        <td data-label="Worker">{{ $suit->worker?->name ?? 'Unassigned' }}</td>
                        <td data-label="Status"><span class="status status-{{ $suit->status }}">{{ ucfirst($suit->status) }}</span></td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    <div class="footer-note">Powered by The Suit Tailor Management System</div>
</div>
</body>
</html>