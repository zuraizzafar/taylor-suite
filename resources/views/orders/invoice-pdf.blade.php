<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; font-size: 11px; color: #1e293b; }

    .page { padding: 18px; }

    /* Invoice copy - repeatable */
    .copy {
        border: 1px solid #cbd5e1;
        border-radius: 4px;
        padding: 14px;
        margin-bottom: 14px;
        page-break-inside: avoid;
    }
    .copy-label {
        background: #0f172a;
        color: #fff;
        font-size: 9px;
        font-weight: bold;
        letter-spacing: 1px;
        padding: 2px 8px;
        border-radius: 2px;
        display: inline-block;
        margin-bottom: 10px;
        text-transform: uppercase;
    }
    .header-row { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 10px; }
    .shop-name { font-size: 15px; font-weight: bold; }
    .sub { font-size: 10px; color: #64748b; margin-top: 2px; }
    .order-info { text-align: right; }
    .order-no { font-size: 13px; font-weight: bold; color: #2563eb; }

    table { width: 100%; border-collapse: collapse; margin-top: 8px; font-size: 10px; }
    th { background: #f1f5f9; text-align: left; padding: 4px 6px; border: 1px solid #e2e8f0; }
    td { padding: 4px 6px; border: 1px solid #e2e8f0; }

    .totals { margin-top: 8px; text-align: right; }
    .totals table { width: 180px; margin-left: auto; }
    .totals td { border: none; padding: 2px 4px; }
    .total-row { font-weight: bold; font-size: 12px; }
    .balance-row { color: #dc2626; }

    .dashed { border-top: 1px dashed #cbd5e1; margin: 10px 0; }
</style>
</head>
<body>
<div class="page">

    @foreach(['CUSTOMER COPY', 'SHOP COPY', 'WORKER COPY'] as $copyLabel)
    <div class="copy">
        <span class="copy-label">{{ $copyLabel }}</span>

        <div class="header-row">
            <div>
                <div class="shop-name">✂️ The Suit Tailor</div>
                <div class="sub">Professional Tailor &amp; Suit Management</div>
            </div>
            <div class="order-info">
                <div class="order-no">{{ $order->order_number }}</div>
                <div class="sub">Order: {{ $order->order_date->format('d M Y') }}</div>
                <div class="sub">Delivery: {{ $order->delivery_date->format('d M Y') }}</div>
            </div>
        </div>

        <div class="dashed"></div>

        <table>
            <tr>
                <td><strong>Customer:</strong> {{ $order->customer->name }}</td>
                <td><strong>File No:</strong> {{ $order->customer->file_number }}</td>
                <td><strong>Mobile:</strong> {{ $order->customer->mobile }}</td>
            </tr>
        </table>

        <table style="margin-top:8px">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Suit Code</th>
                    <th>Suit Type</th>
                    <th>Fabric (m)</th>
                    <th>Worker</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                @foreach($order->suits as $i => $suit)
                <tr>
                    <td>{{ $i + 1 }}</td>
                    <td><strong>{{ $suit->suit_code }}</strong></td>
                    <td>{{ $suit->suit_type }}</td>
                    <td>{{ $suit->fabric_meter }}m {{ $suit->fabric_description }}</td>
                    <td>{{ $suit->worker?->name ?? '—' }}</td>
                    <td>{{ ucfirst($suit->status) }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <div class="totals">
            <table>
                <tr>
                    <td>Total:</td>
                    <td><strong>Rs {{ number_format($order->total_amount) }}</strong></td>
                </tr>
                <tr>
                    <td>Advance:</td>
                    <td>Rs {{ number_format($order->advance_amount) }}</td>
                </tr>
                <tr class="balance-row">
                    <td><strong>Balance:</strong></td>
                    <td><strong>Rs {{ number_format($order->balance_amount) }}</strong></td>
                </tr>
            </table>
        </div>

        @if($order->notes)
        <div style="margin-top:6px;font-size:10px;color:#64748b">Note: {{ $order->notes }}</div>
        @endif
    </div>
    @endforeach

</div>
</body>
</html>
