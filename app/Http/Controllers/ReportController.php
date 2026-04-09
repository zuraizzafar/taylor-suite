<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\Suit;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ReportController extends Controller
{
    public function daily(Request $request): View
    {
        $date   = $request->input('date', today()->toDateString());
        $orders = Order::with(['customer', 'suits'])
            ->whereDate('order_date', $date)
            ->latest()
            ->get();

        return view('reports.daily', compact('orders', 'date'));
    }

    public function pending(Request $request): View
    {
        $status = $request->input('status', '');

        $query = Suit::with(['customer', 'worker', 'order'])
            ->whereNotIn('status', ['delivered']);

        if ($status) {
            $query->where('status', $status);
        }

        $suits = $query->oldest()->get();

        return view('reports.pending', compact('suits', 'status'));
    }

    public function delivered(Request $request): View
    {
        $from = $request->input('from', today()->toDateString());
        $to   = $request->input('to', today()->toDateString());

        $suits = Suit::with(['customer', 'worker', 'order'])
            ->where('status', 'delivered')
            ->whereBetween('delivered_at', [
                $from . ' 00:00:00',
                $to   . ' 23:59:59',
            ])
            ->latest('delivered_at')
            ->get();

        return view('reports.delivered', compact('suits', 'from', 'to'));
    }
}
