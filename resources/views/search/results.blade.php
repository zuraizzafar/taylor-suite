@extends('layouts.app')
@section('title', 'Search Results')
@section('page-title', 'Search: "' . $q . '"')

@section('content')
<div class="pt-2 space-y-6">
    @if(strlen($q) < 2)
    <p class="text-slate-500 text-sm">Enter at least 2 characters to search.</p>
    @else

    {{-- Customers --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700">👤 Customers ({{ $customers->count() }})</h3>
        </div>
        @if($customers->isEmpty())
        <p class="px-5 py-4 text-sm text-slate-400">No customers found.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">File No</th>
                        <th class="px-4 py-2 text-left font-medium">Name</th>
                        <th class="px-4 py-2 text-left font-medium">Mobile</th>
                        <th class="px-4 py-2 text-left font-medium">Suits</th>
                        <th class="px-4 py-2 text-left font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($customers as $c)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 font-mono text-blue-700 font-semibold">{{ $c->file_number }}</td>
                        <td class="px-4 py-2 font-medium text-slate-800">{{ $c->name }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $c->mobile }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $c->suits_count }}</span>
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('customers.show', $c) }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Suits --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700">👔 Suits by Code ({{ $suits->count() }})</h3>
        </div>
        @if($suits->isEmpty())
        <p class="px-5 py-4 text-sm text-slate-400">No suits found.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">Code</th>
                        <th class="px-4 py-2 text-left font-medium">Customer</th>
                        <th class="px-4 py-2 text-left font-medium">Type</th>
                        <th class="px-4 py-2 text-left font-medium">Status</th>
                        <th class="px-4 py-2 text-left font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($suits as $suit)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 font-mono font-semibold text-blue-700">{{ $suit->suit_code }}</td>
                        <td class="px-4 py-2 text-slate-700">{{ $suit->customer->name }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $suit->suit_type }}</td>
                        <td class="px-4 py-2">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $suit->status_badge }}">
                                {{ ucfirst($suit->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('suits.show', $suit) }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Orders --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700">🧾 Orders ({{ $orders->count() }})</h3>
        </div>
        @if($orders->isEmpty())
        <p class="px-5 py-4 text-sm text-slate-400">No orders found.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">Order No</th>
                        <th class="px-4 py-2 text-left font-medium">Customer</th>
                        <th class="px-4 py-2 text-left font-medium">Date</th>
                        <th class="px-4 py-2 text-left font-medium">Total</th>
                        <th class="px-4 py-2 text-left font-medium">Balance</th>
                        <th class="px-4 py-2 text-left font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($orders as $order)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 font-mono font-semibold text-blue-700">{{ $order->order_number }}</td>
                        <td class="px-4 py-2 text-slate-700">{{ $order->customer->name }}</td>
                        <td class="px-4 py-2 text-slate-500 text-xs">{{ $order->order_date->format('d M Y') }}</td>
                        <td class="px-4 py-2 font-medium">Rs {{ number_format($order->total_amount) }}</td>
                        <td class="px-4 py-2 {{ $order->balance_amount > 0 ? 'text-red-600' : 'text-green-600' }} font-medium">
                            Rs {{ number_format($order->balance_amount) }}
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex gap-1">
                                <a href="{{ route('orders.show', $order) }}"
                                   class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">View</a>
                                @if($order->balance_amount > 0)
                                <button onclick="openPayModal({{ $order->id }}, '{{ $order->order_number }}', {{ $order->balance_amount }})"
                                    class="text-xs bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded">+ Pay</button>
                                @endif
                            </div>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Payments --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700">💳 Payments ({{ $payments->count() }})</h3>
        </div>
        @if($payments->isEmpty())
        <p class="px-5 py-4 text-sm text-slate-400">No payments found.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">Date</th>
                        <th class="px-4 py-2 text-left font-medium">Order</th>
                        <th class="px-4 py-2 text-left font-medium">Customer</th>
                        <th class="px-4 py-2 text-left font-medium">Amount</th>
                        <th class="px-4 py-2 text-left font-medium">Method</th>
                        <th class="px-4 py-2 text-left font-medium">Reference</th>
                        <th class="px-4 py-2 text-left font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($payments as $payment)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-slate-600 text-xs">{{ $payment->payment_date->format('d M Y') }}</td>
                        <td class="px-4 py-2 font-mono text-blue-700 font-semibold text-xs">{{ $payment->order->order_number }}</td>
                        <td class="px-4 py-2 text-slate-700">{{ $payment->order->customer->name }}</td>
                        <td class="px-4 py-2 font-semibold text-green-700">Rs {{ number_format($payment->amount) }}</td>
                        <td class="px-4 py-2">
                            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
                                {{ \App\Models\Payment::METHODS[$payment->method] ?? $payment->method }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-slate-500 text-xs">{{ $payment->reference ?? '—' }}</td>
                        <td class="px-4 py-2">
                            <a href="{{ route('orders.show', $payment->order) }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">Order →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    @endif
</div>
@endsection
