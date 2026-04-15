<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Suit;
use App\Models\Worker;
use App\Traits\HasBranchScope;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    use HasBranchScope;

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

        return view('reports.delivered', compact('suits', 'from', 'to'));
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
}
