<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { margin: 0; padding: 0; box-sizing: border-box; }
    body { font-family: DejaVu Sans, sans-serif; color: #1e293b; padding: 24px; }
    .shop-name { font-size: 16px; font-weight: 800; text-align: center; margin-bottom: 2px; }
    .invoice-title { font-size: 10px; text-align: center; color: #64748b; text-transform: uppercase; letter-spacing: 2px; margin-bottom: 16px; }
    .row { display: flex; justify-content: space-between; font-size: 10px; padding: 4px 0; border-bottom: 1px solid #e2e8f0; }
    table { width: 100%; border-collapse: collapse; margin-top: 16px; font-size: 10px; }
    th, td { text-align: left; padding: 6px 4px; border-bottom: 1px solid #e2e8f0; }
    .total-row td { font-weight: 700; font-size: 12px; border-top: 2px solid #1e293b; border-bottom: none; }
</style>
</head>
<body>
    <div class="shop-name">{{ config('app.name', 'Suit Tailor') }}</div>
    <div class="invoice-title">Fabric Sale Invoice</div>

    <div class="row"><span>Invoice No</span><strong>{{ $fabricSale->sale_code }}</strong></div>
    <div class="row"><span>Date</span><strong>{{ $fabricSale->created_at->format('d M Y') }}</strong></div>
    <div class="row"><span>Customer</span><strong>{{ $fabricSale->customer_name }}</strong></div>
    <div class="row"><span>Mobile</span><strong>{{ $fabricSale->customer_mobile ?? '—' }}</strong></div>

    <table>
        <thead>
            <tr>
                <th>Fabric</th>
                <th>Roll No</th>
                <th>Meter</th>
                <th>Rate (Rs)</th>
                <th>Total (Rs)</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ $fabricSale->fabric->fabric_type }} — {{ $fabricSale->fabric->color }}</td>
                <td>{{ $fabricSale->fabric->roll_number }}</td>
                <td>{{ number_format($fabricSale->meter, 2) }}</td>
                <td>{{ number_format($fabricSale->rate, 2) }}</td>
                <td>{{ number_format($fabricSale->total_amount, 2) }}</td>
            </tr>
            <tr class="total-row">
                <td colspan="4">Total</td>
                <td>Rs {{ number_format($fabricSale->total_amount, 2) }}</td>
            </tr>
        </tbody>
    </table>
</body>
</html>
