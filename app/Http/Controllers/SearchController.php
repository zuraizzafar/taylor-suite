<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Suit;
use App\Traits\HasBranchScope;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SearchController extends Controller
{
    use HasBranchScope;

    public function index(Request $request): View
    {
        $q = trim($request->input('q', ''));
        $customers = collect();
        $suits     = collect();
        $orders    = collect();
        $payments  = collect();

        if (strlen($q) >= 2) {
            // Customers
            $customerQuery = Customer::where(function ($cq) use ($q) {
                $cq->where('name', 'like', "%{$q}%")
                   ->orWhere('mobile', 'like', "%{$q}%")
                   ->orWhere('file_number', 'like', "%{$q}%");
            })->withCount('suits');
            $this->branchQuery($customerQuery);
            $customers = $customerQuery->take(20)->get();

            // Suits
            $suitQuery = Suit::with(['customer', 'worker'])
                ->where('suit_code', 'like', "%{$q}%");
            $this->branchQuery($suitQuery);
            $suits = $suitQuery->take(20)->get();

            // Orders
            $orderQuery = Order::with('customer')
                ->where(function ($oq) use ($q) {
                    $oq->where('order_number', 'like', "%{$q}%")
                       ->orWhereHas('customer', fn($cq) => $cq->where('name', 'like', "%{$q}%")
                           ->orWhere('mobile', 'like', "%{$q}%"));
                });
            $this->branchQuery($orderQuery);
            $orders = $orderQuery->latest()->take(20)->get();

            // Payments
            $paymentQuery = Payment::with(['order.customer'])
                ->where(function ($pq) use ($q) {
                    $pq->where('reference', 'like', "%{$q}%")
                       ->orWhereHas('order', fn($oq) => $oq->where('order_number', 'like', "%{$q}%"))
                       ->orWhereHas('order.customer', fn($cq) => $cq->where('name', 'like', "%{$q}%"));
                });
            $this->branchQuery($paymentQuery);
            $payments = $paymentQuery->latest()->take(20)->get();
        }

        return view('search.results', compact('q', 'customers', 'suits', 'orders', 'payments'));
    }
}
