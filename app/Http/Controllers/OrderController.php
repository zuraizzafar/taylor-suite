<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Order;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class OrderController extends Controller
{
    public function index(Request $request): View
    {
        $query = Order::with('customer')
            ->withCount('suits');

        if ($search = $request->input('search')) {
            $query->whereHas('customer', fn($q) =>
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('mobile', 'like', "%{$search}%")
                  ->orWhere('file_number', 'like', "%{$search}%")
            )->orWhere('order_number', 'like', "%{$search}%");
        }

        $orders = $query->latest()->paginate(20)->withQueryString();

        return view('orders.index', compact('orders', 'search'));
    }

    public function create(Request $request): View
    {
        $customers = Customer::orderBy('name')->get();
        $selectedCustomer = $request->input('customer_id')
            ? Customer::find($request->input('customer_id'))
            : null;

        return view('orders.create', compact('customers', 'selectedCustomer'));
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'customer_id'    => ['required', 'exists:customers,id'],
            'order_date'     => ['required', 'date'],
            'delivery_date'  => ['required', 'date', 'after_or_equal:order_date'],
            'total_amount'   => ['required', 'numeric', 'min:0'],
            'advance_amount' => ['required', 'numeric', 'min:0'],
            'notes'          => ['nullable', 'string'],
        ]);

        $data['order_number']   = Order::nextOrderNumber();
        $data['balance_amount'] = $data['total_amount'] - $data['advance_amount'];

        $order = Order::create($data);

        return redirect()->route('orders.show', $order)
            ->with('success', "Order {$order->order_number} created.");
    }

    public function show(Order $order): View
    {
        $order->load([
            'customer',
            'suits.worker',
            'suits.measurement',
        ]);

        return view('orders.show', compact('order'));
    }

    public function edit(Order $order): View
    {
        $customers = Customer::orderBy('name')->get();
        return view('orders.edit', compact('order', 'customers'));
    }

    public function update(Request $request, Order $order): RedirectResponse
    {
        $data = $request->validate([
            'order_date'     => ['required', 'date'],
            'delivery_date'  => ['required', 'date', 'after_or_equal:order_date'],
            'total_amount'   => ['required', 'numeric', 'min:0'],
            'advance_amount' => ['required', 'numeric', 'min:0'],
            'notes'          => ['nullable', 'string'],
        ]);

        $data['balance_amount'] = $data['total_amount'] - $data['advance_amount'];
        $order->update($data);

        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated.');
    }

    public function destroy(Order $order): RedirectResponse
    {
        $order->delete();
        return redirect()->route('orders.index')->with('success', 'Order deleted.');
    }

    public function invoice(Order $order): Response
    {
        $order->load(['customer', 'suits.worker', 'suits.measurement']);

        $pdf = Pdf::loadView('orders.invoice-pdf', compact('order'))
            ->setPaper('a4', 'portrait');

        return $pdf->download("invoice-{$order->order_number}.pdf");
    }
}
