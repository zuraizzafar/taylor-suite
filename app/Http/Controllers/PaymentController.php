<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use App\Models\Payment;
use App\Models\Suit;
use App\Traits\HasBranchScope;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PaymentController extends Controller
{
    use HasBranchScope;

    /**
     * Standalone "Add Payment" page, optionally pre-selecting an order.
     */
    public function create(Request $request): View
    {
        $selectedOrder = null;
        if ($request->filled('order_id')) {
            $selectedOrder = Order::with('customer')->find($request->order_id);
        }
        return view('payments.create', compact('selectedOrder'));
    }

    /**
     * Live AJAX search — returns JSON results for customers, orders, suits.
     */
    public function search(Request $request): JsonResponse
    {
        $q = trim($request->input('q', ''));
        if (strlen($q) < 2) {
            return response()->json(['orders' => [], 'customers' => [], 'suits' => []]);
        }

        // Orders matching order number or customer name/mobile
        $orderQ = Order::with('customer')
            ->where(function ($oq) use ($q) {
                $oq->where('order_number', 'like', "%{$q}%")
                   ->orWhereHas('customer', fn($cq) => $cq
                       ->where('name', 'like', "%{$q}%")
                       ->orWhere('mobile', 'like', "%{$q}%")
                       ->orWhere('file_number', 'like', "%{$q}%"));
            })
            ->where('balance_amount', '>', 0); // Only orders with outstanding balance
        $this->branchQuery($orderQ);
        $orders = $orderQ->latest()->take(15)->get()->map(fn($o) => [
            'id'             => $o->id,
            'order_number'   => $o->order_number,
            'customer_name'  => $o->customer->name,
            'customer_mobile'=> $o->customer->mobile,
            'total_amount'   => $o->total_amount,
            'balance_amount' => $o->balance_amount,
            'order_date'     => $o->order_date->format('d M Y'),
        ]);

        // Customers with pending balance
        $custQ = Customer::where(function ($cq) use ($q) {
            $cq->where('name', 'like', "%{$q}%")
               ->orWhere('mobile', 'like', "%{$q}%")
               ->orWhere('file_number', 'like', "%{$q}%");
        })->whereHas('orders', fn($oq) => $oq->where('balance_amount', '>', 0));
        $this->branchQuery($custQ);
        $customers = $custQ->take(8)->get()->map(fn($c) => [
            'id'          => $c->id,
            'name'        => $c->name,
            'mobile'      => $c->mobile,
            'file_number' => $c->file_number,
        ]);

        // Suits — search by code, link to their order
        $suitQ = Suit::with(['customer', 'order'])
            ->where('suit_code', 'like', "%{$q}%")
            ->whereHas('order', fn($oq) => $oq->where('balance_amount', '>', 0));
        $this->branchQuery($suitQ);
        $suits = $suitQ->take(8)->get()->map(fn($s) => [
            'id'            => $s->order_id,
            'suit_code'     => $s->suit_code,
            'customer_name' => $s->customer->name,
            'order_number'  => $s->order?->order_number,
            'balance_amount'=> $s->order?->balance_amount,
        ])->filter(fn($s) => $s['id']);

        return response()->json(compact('orders', 'customers', 'suits'));
    }

    public function store(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'method'       => ['required', 'in:cash,bank_transfer,cheque,online'],
            'payment_date' => ['required', 'date'],
            'reference'    => ['nullable', 'string', 'max:100'],
            'note'         => ['nullable', 'string', 'max:500'],
        ]);

        $remaining = (float) $order->balance_amount;
        if ((float) $data['amount'] > $remaining) {
            return back()->withInput()->with('error',
                "Payment amount (Rs " . number_format($data['amount']) . ") exceeds the remaining balance (Rs " . number_format($remaining) . ")."
            );
        }

        $data['order_id']    = $order->id;
        $data['branch_id']   = $order->branch_id;
        $data['received_by'] = auth()->id();

        Payment::create($data);
        $order->recalculateBalance();

        return back()->with('success', 'Payment of Rs ' . number_format($data['amount']) . ' recorded.');
    }

    public function edit(Payment $payment): View
    {
        $payment->load('order.customer');
        return view('payments.edit', compact('payment'));
    }

    public function update(Request $request, Payment $payment): RedirectResponse
    {
        $data = $request->validate([
            'amount'       => ['required', 'numeric', 'min:0.01'],
            'method'       => ['required', 'in:cash,bank_transfer,cheque,online'],
            'payment_date' => ['required', 'date'],
            'reference'    => ['nullable', 'string', 'max:100'],
            'note'         => ['nullable', 'string', 'max:500'],
        ]);

        $order     = $payment->order;
        $available = (float) $order->balance_amount + (float) $payment->amount;

        if ((float) $data['amount'] > $available) {
            return back()->withInput()->with('error',
                "Amount (Rs " . number_format($data['amount']) . ") exceeds available balance (Rs " . number_format($available) . ")."
            );
        }

        $payment->update($data);
        $order->recalculateBalance();

        return redirect()->route('orders.show', $order)->with('success', 'Payment updated.');
    }

    public function destroy(Payment $payment): RedirectResponse
    {
        $order = $payment->order;
        $payment->delete();
        $order->recalculateBalance();

        return back()->with('success', 'Payment removed and balance updated.');
    }
}
