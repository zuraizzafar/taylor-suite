@extends('layouts.app')
@section('title', 'Daily Orders Report')
@section('page-title', 'Daily Orders Report')

@section('content')
<div class="pt-2">
    <form method="GET" action="{{ route('reports.daily') }}" class="flex gap-2 mb-5">
        <input type="date" name="date" value="{{ $date }}"
            class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        <button class="bg-slate-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-slate-800">Filter</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-700">Orders on {{ \Carbon\Carbon::parse($date)->format('d M Y') }}</h3>
            <span class="text-xs text-slate-500">{{ $orders->count() }} order(s)</span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-2 text-left font-medium">Order No</th>
                    <th class="px-4 py-2 text-left font-medium">Customer</th>
                    <th class="px-4 py-2 text-left font-medium">Delivery</th>
                    <th class="px-4 py-2 text-left font-medium">Suits</th>
                    <th class="px-4 py-2 text-left font-medium">Total</th>
                    <th class="px-4 py-2 text-left font-medium">Balance</th>
                    <th class="px-4 py-2 text-left font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($orders as $order)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2 font-mono text-blue-700 font-semibold">{{ $order->order_number }}</td>
                    <td class="px-4 py-2 font-medium text-slate-800">{{ $order->customer->name }}</td>
                    <td class="px-4 py-2 text-slate-600">{{ $order->delivery_date->format('d M Y') }}</td>
                    <td class="px-4 py-2 text-center">
                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $order->suits->count() }}</span>
                    </td>
                    <td class="px-4 py-2 font-medium">Rs {{ number_format($order->total_amount) }}</td>
                    <td class="px-4 py-2 {{ $order->balance_amount > 0 ? 'text-red-600' : 'text-green-600' }} font-medium">
                        Rs {{ number_format($order->balance_amount) }}
                    </td>
                    <td class="px-4 py-2">
                        <a href="{{ route('orders.show', $order) }}"
                           class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No orders on this date.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
