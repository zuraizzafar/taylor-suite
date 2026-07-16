<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\Customer;
use App\Models\FabricSale;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Suit;
use App\Models\Worker;
use App\Models\WorkerSalaryPayment;
use App\Traits\HasBranchScope;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    use HasBranchScope;

    public function index(): View
    {
        return view('reports.index');
    }

    public function daily(Request $request): View
    {
        $date  = $request->input('date', today()->toDateString());
        $query = Order::with(['customer', 'suits'])->whereDate('order_date', $date);
        $this->branchQuery($query);
        $orders = $query->latest()->get();

        return view('reports.daily', compact('orders', 'date'));
    }

    public function pending(Request $request): View
    {
        $status = $request->input('status', '');
        $query  = Suit::with(['customer', 'worker', 'order'])->whereNotIn('status', ['delivered']);
        $this->branchQuery($query);

        if ($status) {
            $query->where('status', $status);
        }

        $suits = $query->oldest()->get();

        return view('reports.pending', compact('suits', 'status'));
    }

    public function delivered(Request $request): View
    {
        $from  = $request->input('from', today()->toDateString());
        $to    = $request->input('to', today()->toDateString());
        $query = Suit::with(['customer', 'worker', 'order'])
            ->where('status', 'delivered')
            ->whereBetween('delivered_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        $this->branchQuery($query);

        $suits = $query->latest('delivered_at')->get();

        // Today's delivery summary card
        $todayQuery = Suit::with('order')->where('status', 'delivered')
            ->whereDate('delivered_at', today());
        $this->branchQuery($todayQuery);
        $todaySuits      = $todayQuery->get();
        $todayCount      = $todaySuits->count();
        $todayWorth      = $todaySuits->sum(fn($s) => (float) ($s->order->total_amount ?? 0));

        return view('reports.delivered', compact('suits', 'from', 'to', 'todayCount', 'todayWorth'));
    }

    public function salary(Request $request): View
    {
        $from = $request->input('from', today()->startOfMonth()->toDateString());
        $to   = $request->input('to', today()->toDateString());

        $workerQuery = Worker::with(['suits' => function ($q) use ($from, $to) {
            $q->whereNotNull('stitching_started_at')
              ->whereBetween('stitching_started_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
              ->with('customer');
        }])->where('is_active', true);

        $this->branchQuery($workerQuery);

        $workers     = $workerQuery->get()->filter(fn($w) => $w->suits->isNotEmpty());
        $totalPayout = $workers->sum(fn($w) => $w->suits->sum('worker_earning'));

        return view('reports.salary', compact('workers', 'from', 'to', 'totalPayout'));
    }

    public function pendingBalances(Request $request): View
    {
        $query = Customer::with(['orders' => fn($q) => $q->where('balance_amount', '>', 0)])
            ->whereHas('orders', fn($q) => $q->where('balance_amount', '>', 0));
        $this->branchQuery($query);

        $customers = $query->get()
            ->map(function ($c) {
                $c->total_outstanding = $c->orders->sum('balance_amount');
                return $c;
            })
            ->sortByDesc('total_outstanding');

        $grandTotal = $customers->sum('total_outstanding');

        return view('reports.pending-balances', compact('customers', 'grandTotal'));
    }

    public function payments(Request $request): View
    {
        $from   = $request->input('from', today()->startOfMonth()->toDateString());
        $to     = $request->input('to', today()->toDateString());
        $method = $request->input('method', '');

        $query = Payment::with(['order.customer', 'receivedBy'])
            ->whereBetween('payment_date', [$from, $to]);

        $this->branchQuery($query);

        if ($method) {
            $query->where('method', $method);
        }

        $payments     = $query->latest('payment_date')->get();
        $totalAmount  = $payments->sum('amount');
        $byMethod     = $payments->groupBy('method')->map->sum('amount');
        $methods      = \App\Models\Payment::METHODS;

        return view('reports.payments', compact('payments', 'from', 'to', 'method', 'totalAmount', 'byMethod', 'methods'));
    }

    public function workers(Request $request): View
    {
        $from = $request->input('from', today()->startOfMonth()->toDateString());
        $to   = $request->input('to', today()->toDateString());

        $workerQuery = Worker::with([
            'branch',
            'suits' => function ($q) use ($from, $to) {
                $q->whereNotNull('stitching_started_at')
                  ->whereBetween('stitching_started_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
            },
            'salaryPayments',
        ])->where('is_active', true);

        $this->branchQuery($workerQuery);

        $workers = $workerQuery->get()->map(function ($w) {
            $w->period_suits   = $w->suits->count();
            $w->period_earned  = (float) $w->suits->sum('worker_earning');
            $w->total_paid     = (float) $w->salaryPayments->sum('amount_paid');
            $w->balance_due    = max(0, $w->period_earned - $w->total_paid);
            return $w;
        })->sortByDesc('period_suits');

        $totalSuits  = $workers->sum('period_suits');
        $totalEarned = $workers->sum('period_earned');
        $totalPaid   = $workers->sum('total_paid');

        return view('reports.workers', compact('workers', 'from', 'to', 'totalSuits', 'totalEarned', 'totalPaid'));
    }

    // ── Comprehensive Salary Report ──────────────────────────────────────────
    public function salaryReport(Request $request): View
    {
        [$from, $to, $preset] = $this->resolvePeriod($request);

        $workerQuery = Worker::with([
            'branch',
            'suits' => function ($q) use ($from, $to) {
                $q->whereNotNull('stitching_started_at')
                  ->whereBetween('stitching_started_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                  ->with('stitchType', 'customer', 'order');
            },
            'salaryPayments' => function ($q) use ($from, $to) {
                $q->orderBy('paid_at');
            },
        ])->where('is_active', true);

        $this->branchQuery($workerQuery);
        $branches = Branch::where('is_active', true)->orderBy('name')->get();

        $workers = $workerQuery->orderBy('name')->get()->map(function ($w) use ($from, $to) {
            // Period suits + earned
            $w->period_suits   = $w->suits->count();
            $w->period_earned  = (float) $w->suits->sum('worker_earning');

            // Group suits by stitch type for breakdown
            $w->by_stitch_type = $w->suits->groupBy(fn($s) => $s->stitchType?->name ?? 'Unspecified')
                ->map(fn($suits) => [
                    'count'  => $suits->count(),
                    'earned' => (float) $suits->sum('worker_earning'),
                ]);

            // All-time paid
            $w->total_paid_alltime = (float) $w->salaryPayments->sum('amount_paid');

            // Paid within this period
            $w->period_paid = (float) $w->salaryPayments
                ->filter(fn($p) => $p->paid_at && $p->paid_at->between(
                    \Carbon\Carbon::parse($from)->startOfDay(),
                    \Carbon\Carbon::parse($to)->endOfDay()
                ))
                ->sum('amount_paid');

            // Balance = all time earned – all time paid (running balance)
            // Recalculate all-time earned for accurate balance
            $allTimeEarned = (float) $w->suits()
                ->whereNotNull('stitching_started_at')
                ->sum('worker_earning');
            $w->balance_due = max(0, $allTimeEarned - $w->total_paid_alltime);

            return $w;
        })->sortByDesc('period_suits');

        $totalSuits   = $workers->sum('period_suits');
        $totalEarned  = $workers->sum('period_earned');
        $totalPaid    = $workers->sum('period_paid');
        $totalBalance = $workers->sum('balance_due');

        return view('reports.salary-report', compact(
            'workers', 'from', 'to', 'preset',
            'totalSuits', 'totalEarned', 'totalPaid', 'totalBalance',
            'branches'
        ));
    }

    public function salaryReportPdf(Request $request)
    {
        [$from, $to] = $this->resolvePeriod($request);

        $workerQuery = Worker::with([
            'branch',
            'suits' => function ($q) use ($from, $to) {
                $q->whereNotNull('stitching_started_at')
                  ->whereBetween('stitching_started_at', [$from . ' 00:00:00', $to . ' 23:59:59'])
                  ->with('stitchType');
            },
            'salaryPayments',
        ])->where('is_active', true);

        $this->branchQuery($workerQuery);

        $workers = $workerQuery->orderBy('name')->get()->map(function ($w) {
            $w->period_suits   = $w->suits->count();
            $w->period_earned  = (float) $w->suits->sum('worker_earning');
            $allTimeEarned     = (float) $w->suits()->whereNotNull('stitching_started_at')->sum('worker_earning');
            $w->total_paid     = (float) $w->salaryPayments->sum('amount_paid');
            $w->balance_due    = max(0, $allTimeEarned - $w->total_paid);
            return $w;
        });

        $totalSuits   = $workers->sum('period_suits');
        $totalEarned  = $workers->sum('period_earned');
        $totalBalance = $workers->sum('balance_due');
        $setting      = \App\Models\Setting::pluck('value', 'key');

        $pdf = Pdf::loadView('reports.salary-report-pdf', compact(
            'workers', 'from', 'to', 'totalSuits', 'totalEarned', 'totalBalance', 'setting'
        ))->setPaper('a4', 'landscape');

        return $pdf->stream("salary-report-{$from}-{$to}.pdf");
    }

    public function fabricProfit(Request $request): View
    {
        [$from, $to, $preset] = $this->resolvePeriod($request);

        $salesQuery = FabricSale::with('fabric')
            ->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
        $this->branchQuery($salesQuery);

        $sales = $salesQuery->latest()->get();

        $cost   = (float) $sales->sum(fn($s) => $s->meter * ($s->fabric->cost_price ?? 0));
        $sale   = (float) $sales->sum('total_amount');
        $profit = $sale - $cost;

        return view('reports.fabric-profit', compact('sales', 'from', 'to', 'preset', 'cost', 'sale', 'profit'));
    }

    public function exportCsv(Request $request, string $report)
    {
        $filename = "report-{$report}-" . now()->format('Y-m-d') . ".csv";

        $headers = [
            "Content-type"        => "text/csv",
            "Content-Disposition" => "attachment; filename={$filename}",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        $callback = function() use ($request, $report) {
            $file = fopen('php://output', 'w');
            
            // Add BOM for correct UTF-8 loading in Excel (Urdu support)
            fprintf($file, chr(0xEF).chr(0xBB).chr(0xBF));

            switch ($report) {
                case 'daily':
                    $date  = $request->input('date', today()->toDateString());
                    $query = Order::with(['customer', 'suits'])->whereDate('order_date', $date);
                    $this->branchQuery($query);
                    $orders = $query->latest()->get();

                    fputcsv($file, ['Daily Orders Report', 'Date:', $date]);
                    fputcsv($file, []);
                    fputcsv($file, ['Order #', 'Customer', 'Mobile', 'Suits Count', 'Total Amount', 'Advance', 'Balance', 'Delivery Date']);

                    foreach ($orders as $order) {
                        fputcsv($file, [
                            $order->order_number,
                            $order->customer->name,
                            $order->customer->mobile,
                            $order->suits->count(),
                            $order->total_amount,
                            $order->advance_amount,
                            $order->balance_amount,
                            $order->delivery_date?->toDateString() ?? '—'
                        ]);
                    }
                    break;

                case 'pending':
                    $status = $request->input('status', '');
                    $query  = Suit::with(['customer', 'worker', 'order'])->whereNotIn('status', ['delivered']);
                    $this->branchQuery($query);
                    if ($status) { $query->where('status', $status); }
                    $suits = $query->oldest()->get();

                    fputcsv($file, ['Pending Suits Report', 'Status:', $status ?: 'All Pending']);
                    fputcsv($file, []);
                    fputcsv($file, ['Suit Code', 'Order #', 'Customer', 'Type', 'Worker', 'Status', 'Delivery Date']);

                    foreach ($suits as $suit) {
                        fputcsv($file, [
                            $suit->suit_code,
                            $suit->order->order_number,
                            $suit->customer->name,
                            $suit->suit_type,
                            $suit->worker?->name ?? '—',
                            ucfirst($suit->status),
                            $suit->order->delivery_date?->toDateString() ?? '—'
                        ]);
                    }
                    break;

                case 'delivered':
                    $from  = $request->input('from', today()->toDateString());
                    $to    = $request->input('to', today()->toDateString());
                    $query = Suit::with(['customer', 'worker', 'order'])
                        ->where('status', 'delivered')
                        ->whereBetween('delivered_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
                    $this->branchQuery($query);
                    $suits = $query->latest('delivered_at')->get();

                    fputcsv($file, ['Delivered Suits Report', 'From:', $from, 'To:', $to]);
                    fputcsv($file, []);
                    fputcsv($file, ['Suit Code', 'Order #', 'Customer', 'Type', 'Worker', 'Delivered At', 'Total Amount']);

                    foreach ($suits as $suit) {
                        fputcsv($file, [
                            $suit->suit_code,
                            $suit->order->order_number,
                            $suit->customer->name,
                            $suit->suit_type,
                            $suit->worker?->name ?? '—',
                            $suit->delivered_at?->toDateTimeString() ?? '—',
                            $suit->order->total_amount
                        ]);
                    }
                    break;

                case 'salary':
                    $from = $request->input('from', today()->startOfMonth()->toDateString());
                    $to   = $request->input('to', today()->toDateString());
                    $workerQuery = Worker::with(['suits' => function ($q) use ($from, $to) {
                        $q->whereNotNull('stitching_started_at')
                          ->whereBetween('stitching_started_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
                    }])->where('is_active', true);
                    $this->branchQuery($workerQuery);
                    $workers = $workerQuery->get()->filter(fn($w) => $w->suits->isNotEmpty());

                    fputcsv($file, ['Salary Summary Report', 'From:', $from, 'To:', $to]);
                    fputcsv($file, []);
                    fputcsv($file, ['Worker Name', 'Mobile', 'Suits Stitched', 'Earning (Rs)']);

                    foreach ($workers as $w) {
                        fputcsv($file, [
                            $w->name,
                            $w->mobile,
                            $w->suits->count(),
                            $w->suits->sum('worker_earning')
                        ]);
                    }
                    break;

                case 'pending-balances':
                    $query = Customer::with(['orders' => fn($q) => $q->where('balance_amount', '>', 0)])
                        ->whereHas('orders', fn($q) => $q->where('balance_amount', '>', 0));
                    $this->branchQuery($query);
                    $customers = $query->get()->map(function ($c) {
                        $c->total_outstanding = $c->orders->sum('balance_amount');
                        return $c;
                    })->sortByDesc('total_outstanding');

                    fputcsv($file, ['Pending Balances Report']);
                    fputcsv($file, []);
                    fputcsv($file, ['Customer Name', 'Mobile', 'Outstanding Amount (Rs)']);

                    foreach ($customers as $c) {
                        fputcsv($file, [
                            $c->name,
                            $c->mobile,
                            $c->total_outstanding
                        ]);
                    }
                    break;

                case 'payments':
                    $from   = $request->input('from', today()->startOfMonth()->toDateString());
                    $to     = $request->input('to', today()->toDateString());
                    $method = $request->input('method', '');
                    $query = Payment::with(['order.customer', 'receivedBy'])->whereBetween('payment_date', [$from, $to]);
                    $this->branchQuery($query);
                    if ($method) { $query->where('method', $method); }
                    $payments = $query->latest('payment_date')->get();

                    fputcsv($file, ['Payments Received Report', 'From:', $from, 'To:', $to, 'Method:', $method ?: 'All']);
                    fputcsv($file, []);
                    fputcsv($file, ['Payment Date', 'Order #', 'Customer', 'Amount (Rs)', 'Method', 'Received By']);

                    foreach ($payments as $p) {
                        fputcsv($file, [
                            $p->payment_date->toDateString(),
                            $p->order->order_number,
                            $p->order->customer->name,
                            $p->amount,
                            ucfirst($p->method),
                            $p->receivedBy?->name ?? '—'
                        ]);
                    }
                    break;

                case 'workers':
                    $from = $request->input('from', today()->startOfMonth()->toDateString());
                    $to   = $request->input('to', today()->toDateString());
                    $workerQuery = Worker::with([
                        'branch',
                        'suits' => function ($q) use ($from, $to) {
                            $q->whereNotNull('stitching_started_at')
                              ->whereBetween('stitching_started_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
                        },
                        'salaryPayments',
                    ])->where('is_active', true);
                    $this->branchQuery($workerQuery);
                    $workers = $workerQuery->get()->map(function ($w) {
                        $w->period_suits   = $w->suits->count();
                        $w->period_earned  = (float) $w->suits->sum('worker_earning');
                        $w->total_paid     = (float) $w->salaryPayments->sum('amount_paid');
                        $w->balance_due    = max(0, $w->period_earned - $w->total_paid);
                        return $w;
                    })->sortByDesc('period_suits');

                    fputcsv($file, ['Workers Summary Report', 'From:', $from, 'To:', $to]);
                    fputcsv($file, []);
                    fputcsv($file, ['Worker Name', 'Branch', 'Suits Stitched', 'Amount Earned (Rs)', 'Amount Paid (Rs)', 'Balance Due (Rs)']);

                    foreach ($workers as $w) {
                        fputcsv($file, [
                            $w->name,
                            $w->branch?->name ?? '—',
                            $w->period_suits,
                            $w->period_earned,
                            $w->total_paid,
                            $w->balance_due
                        ]);
                    }
                    break;

                case 'fabric-profit':
                    [$from, $to, $preset] = $this->resolvePeriod($request);
                    $salesQuery = FabricSale::with('fabric')->whereBetween('created_at', [$from . ' 00:00:00', $to . ' 23:59:59']);
                    $this->branchQuery($salesQuery);
                    $sales = $salesQuery->latest()->get();

                    fputcsv($file, ['Fabric Profit Report', 'From:', $from, 'To:', $to]);
                    fputcsv($file, []);
                    fputcsv($file, ['Sale Code', 'Date', 'Fabric Type', 'Roll #', 'Meters Sold', 'Rate (Rs)', 'Total Amount (Rs)', 'Estimated Profit (Rs)']);

                    foreach ($sales as $s) {
                        $cost = $s->meter * ($s->fabric->cost_price ?? 0);
                        $profit = $s->total_amount - $cost;
                        fputcsv($file, [
                            $s->sale_code,
                            $s->created_at->toDateString(),
                            $s->fabric->fabric_type,
                            $s->fabric->roll_number,
                            $s->meter,
                            $s->rate,
                            $s->total_amount,
                            $profit
                        ]);
                    }
                    break;
            }

            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    // ── Helper ───────────────────────────────────────────────────────────────
    private function resolvePeriod(Request $request): array
    {
        $preset = $request->input('preset', 'month');
        $from   = $request->input('from');
        $to     = $request->input('to');

        if (! $from || ! $to) {
            [$from, $to] = match ($preset) {
                'today'      => [today()->toDateString(), today()->toDateString()],
                'week'       => [today()->startOfWeek()->toDateString(), today()->endOfWeek()->toDateString()],
                'last_month' => [today()->subMonth()->startOfMonth()->toDateString(), today()->subMonth()->endOfMonth()->toDateString()],
                default      => [today()->startOfMonth()->toDateString(), today()->toDateString()],
            };
        }

        return [$from, $to, $preset];
    }
}

