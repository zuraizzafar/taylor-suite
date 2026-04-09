<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Suit;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function index(): View
    {
        $stats = [
            'total_customers'  => Customer::count(),
            'orders_today'     => Order::whereDate('order_date', today())->count(),
            'pending_suits'    => Suit::whereIn('status', ['pending', 'cutting', 'stitching'])->count(),
            'ready_suits'      => Suit::where('status', 'ready')->count(),
            'delivered_today'  => Suit::where('status', 'delivered')
                                        ->whereDate('delivered_at', today())->count(),
            'total_suits'      => Suit::count(),
        ];

        $recent_orders = Order::with('customer')
            ->latest()
            ->take(5)
            ->get();

        $pending_suits = Suit::with(['customer', 'worker'])
            ->whereIn('status', ['pending', 'cutting', 'stitching', 'ready'])
            ->orderBy('created_at')
            ->take(10)
            ->get();

        return view('dashboard', compact('stats', 'recent_orders', 'pending_suits'));
    }
}
