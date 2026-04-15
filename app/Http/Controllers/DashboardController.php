<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Expense;
use App\Models\Order;
use App\Models\Suit;
use App\Traits\HasBranchScope;
use Illuminate\View\View;

class DashboardController extends Controller
{
    use HasBranchScope;

    public function index(): View
    {
        $bid = $this->currentBranchId();

        $statsBase = fn($model) => $bid ? $model::where('branch_id', $bid) : $model::query();

        $stats = [
            'total_customers' => (clone $statsBase(Customer::class))->count(),
            'orders_today'    => (clone $statsBase(Order::class))->whereDate('order_date', today())->count(),
            'pending_suits'   => (clone $statsBase(Suit::class))->whereIn('status', ['pending', 'cutting', 'stitching'])->count(),
            'ready_suits'     => (clone $statsBase(Suit::class))->where('status', 'ready')->count(),
            'delivered_today' => (clone $statsBase(Suit::class))->where('status', 'delivered')->whereDate('delivered_at', today())->count(),
            'total_suits'     => (clone $statsBase(Suit::class))->count(),
        ];

        // ── Finance Overview (scoped to branch) ───────────────
        $orderQ   = $bid ? Order::where('branch_id', $bid) : Order::query();
        $suitQ    = $bid ? Suit::where('branch_id', $bid)  : Suit::query();
        $expenseQ = $bid ? Expense::where('branch_id', $bid) : Expense::query();

        $totalRevenue     = (float) (clone $orderQ)->sum('total_amount');
        $totalCollected   = (float) (clone $orderQ)->sum('advance_amount');
        $totalOutstanding = (float) (clone $orderQ)->where('balance_amount', '>', 0)->sum('balance_amount');
        $workerSalaries   = (float) (clone $suitQ)->whereNotNull('worker_earning')->sum('worker_earning');
        $totalExpenses    = (float) (clone $expenseQ)->sum('amount');
        $netProfit        = $totalCollected - $workerSalaries - $totalExpenses;

        $finance = compact(
            'totalRevenue', 'totalCollected', 'totalOutstanding',
            'workerSalaries', 'totalExpenses', 'netProfit'
        );

        $recent_orders = (clone $orderQ)->with('customer')->latest()->take(5)->get();

        $pending_suits = (clone $suitQ)->with(['customer', 'worker'])
            ->whereIn('status', ['pending', 'cutting', 'stitching', 'ready'])
            ->orderBy('created_at')
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'finance', 'recent_orders', 'pending_suits'));
    }
}
