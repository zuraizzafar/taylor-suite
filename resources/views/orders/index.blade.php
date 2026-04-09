@extends('layouts.app')
@section('title', 'Orders')
@section('page-title', 'Orders')

@section('content')
<div class="pt-2">
    <div class="flex items-center justify-between mb-5">
        <form method="GET" action="{{ route('orders.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ $search }}"
                placeholder="Search order, customer…"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <button class="bg-slate-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-slate-800">Search</button>
        </form>
        <a href="{{ route('orders.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + New Order
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Order No</th>
                    <th class="px-4 py-3 text-left font-medium">Customer</th>
                    <th class="px-4 py-3 text-left font-medium">Order Date</th>
                    <th class="px-4 py-3 text-left font-medium">Delivery</th>
                    <th class="px-4 py-3 text-left font-medium">Suits</th>
                    <th class="px-4 py-3 text-left font-medium">Total</th>
                    <th class="px-4 py-3 text-left font-medium">Balance</th>
                    <th class="px-4 py-3 text-left font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-mono text-blue-700 font-semibold">{{ $order->order_number }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-800">{{ $order->customer->name }}</p>
                        <p class="text-xs text-slate-500">{{ $order->customer->file_number }}</p>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $order->order_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $order->delivery_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $order->suits_count }}</span>
                    </td>
                    <td class="px-4 py-3 font-medium">Rs {{ number_format($order->total_amount) }}</td>
                    <td class="px-4 py-3 {{ $order->balance_amount > 0 ? 'text-red-600 font-medium' : 'text-green-600' }}">
                        Rs {{ number_format($order->balance_amount) }}
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1">
                            <a href="{{ route('orders.show', $order) }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">View</a>
                            <a href="{{ route('orders.invoice', $order) }}"
                               class="text-xs bg-green-50 hover:bg-green-100 text-green-700 px-2 py-1 rounded">PDF</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">No orders yet.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($orders->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">{{ $orders->links() }}</div>
        @endif
    </div>
</div>
@endsection
