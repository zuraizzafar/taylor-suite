<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<style>
    * { box-sizing: border-box; margin: 0; padding: 0; font-family: DejaVu Sans, sans-serif; }
    body { font-size: 10px; color: #1e293b; background: #fff; padding: 20px; }
    .header { display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 16px; border-bottom: 2px solid #1e40af; padding-bottom: 10px; }
    .shop-name { font-size: 16px; font-weight: bold; color: #1e40af; }
    .report-title { font-size: 12px; color: #475569; margin-top: 2px; }
    .period { font-size: 9px; color: #64748b; margin-top: 2px; }
    .summary { display: flex; gap: 10px; margin-bottom: 16px; }
    .summary-card { flex: 1; background: #f1f5f9; border-radius: 6px; padding: 8px 10px; }
    .summary-label { font-size: 8px; color: #64748b; text-transform: uppercase; letter-spacing: 0.05em; }
    .summary-value { font-size: 13px; font-weight: bold; color: #1e293b; margin-top: 2px; }
    table { width: 100%; border-collapse: collapse; margin-bottom: 10px; }
    thead th { background: #1e40af; color: #fff; padding: 5px 7px; text-align: left; font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 0.04em; }
    thead th.right { text-align: right; }
    thead th.center { text-align: center; }
    tbody tr:nth-child(even) { background: #f8fafc; }
    tbody td { padding: 5px 7px; border-bottom: 1px solid #e2e8f0; font-size: 9px; vertical-align: middle; }
    tbody td.right { text-align: right; }
    tbody td.center { text-align: center; }
    .worker-name { font-weight: bold; color: #1e293b; }
    .branch-tag { font-size: 8px; color: #64748b; }
    .due { color: #dc2626; font-weight: bold; }
    .paid { color: #16a34a; }
    tfoot td { padding: 6px 7px; font-weight: bold; border-top: 2px solid #1e40af; font-size: 9px; }
    tfoot td.right { text-align: right; }
    .footer { margin-top: 20px; border-top: 1px solid #e2e8f0; padding-top: 8px; font-size: 8px; color: #94a3b8; text-align: right; }
    .print-date { font-size: 8px; color: #94a3b8; }
</style>
</head>
<body>
<div class="header">
    <div>
        <div class="shop-name">✂ {{ $setting['shop_name'] ?? 'The Suit Tailor' }}</div>
        <div class="report-title">Salary Report</div>
        <div class="period">Period: {{ \Carbon\Carbon::parse($from)->format('d M Y') }} — {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
    </div>
    <div class="print-date">Printed: {{ now()->format('d M Y H:i') }}</div>
</div>

<div class="summary">
    <div class="summary-card">
        <div class="summary-label">Suits Stitched</div>
        <div class="summary-value">{{ $totalSuits }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Total Earned</div>
        <div class="summary-value">Rs {{ number_format($totalEarned) }}</div>
    </div>
    <div class="summary-card">
        <div class="summary-label">Balance Due</div>
        <div class="summary-value" style="color:{{ $totalBalance > 0 ? '#dc2626' : '#16a34a' }}">Rs {{ number_format($totalBalance) }}</div>
    </div>
</div>

<table>
    <thead>
        <tr>
            <th>#</th>
            <th>Worker</th>
            <th>Branch</th>
            <th class="center">Suits</th>
            <th class="right">Earned (Period)</th>
            <th class="right">Total Paid</th>
            <th class="right">Balance Due</th>
        </tr>
    </thead>
    <tbody>
        @foreach($workers as $i => $worker)
        @if($worker->period_suits > 0 || $worker->balance_due > 0)
        <tr>
            <td>{{ $i + 1 }}</td>
            <td class="worker-name">{{ $worker->name }}</td>
            <td class="branch-tag">{{ $worker->branch?->name ?? '—' }}</td>
            <td class="center">{{ $worker->period_suits }}</td>
            <td class="right">Rs {{ number_format($worker->period_earned) }}</td>
            <td class="right paid">Rs {{ number_format($worker->total_paid) }}</td>
            <td class="right {{ $worker->balance_due > 0 ? 'due' : '' }}">Rs {{ number_format($worker->balance_due) }}</td>
        </tr>
        @endif
        @endforeach
    </tbody>
    <tfoot>
        <tr>
            <td colspan="3">Total</td>
            <td class="right center">{{ $totalSuits }}</td>
            <td class="right">Rs {{ number_format($totalEarned) }}</td>
            <td class="right"></td>
            <td class="right {{ $totalBalance > 0 ? 'due' : '' }}">Rs {{ number_format($totalBalance) }}</td>
        </tr>
    </tfoot>
</table>

<div class="footer">{{ $setting['shop_name'] ?? 'The Suit Tailor' }} · Salary Report · {{ \Carbon\Carbon::parse($from)->format('d M Y') }} to {{ \Carbon\Carbon::parse($to)->format('d M Y') }}</div>
</body>
</html>
