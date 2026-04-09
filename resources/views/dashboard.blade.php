@extends('layouts.app')

@section('title', 'Dashboard – Suit Tailor')
@section('page-title', 'Dashboard')

@section('content')
<div class="pt-2">

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-8">
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-500 mb-1">Total Customers</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['total_customers'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-500 mb-1">Orders Today</p>
            <p class="text-2xl font-bold text-blue-600">{{ $stats['orders_today'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-500 mb-1">Pending Suits</p>
            <p class="text-2xl font-bold text-yellow-600">{{ $stats['pending_suits'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-500 mb-1">Ready to Deliver</p>
            <p class="text-2xl font-bold text-green-600">{{ $stats['ready_suits'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-500 mb-1">Delivered Today</p>
            <p class="text-2xl font-bold text-purple-600">{{ $stats['delivered_today'] }}</p>
        </div>
        <div class="bg-white rounded-xl p-4 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-500 mb-1">Total Suits</p>
            <p class="text-2xl font-bold text-slate-800">{{ $stats['total_suits'] }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

        {{-- Recent Orders --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-700">Recent Orders</h3>
                <a href="{{ route('orders.index') }}" class="text-xs text-blue-600 hover:underline">View all →</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($recent_orders as $order)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $order->customer->name }}</p>
                        <p class="text-xs text-slate-500">{{ $order->order_number }} · {{ $order->order_date->format('d M Y') }}</p>
                    </div>
                    <div class="text-right">
                        <p class="text-sm font-semibold text-slate-700">Rs {{ number_format($order->total_amount) }}</p>
                        <p class="text-xs text-red-500">Bal: Rs {{ number_format($order->balance_amount) }}</p>
                    </div>
                </div>
                @empty
                <p class="px-5 py-4 text-sm text-slate-400">No orders yet.</p>
                @endforelse
            </div>
        </div>

        {{-- Active Suits --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100">
            <div class="flex items-center justify-between px-5 py-4 border-b border-slate-100">
                <h3 class="font-semibold text-slate-700">Active Suits</h3>
                <a href="{{ route('suits.index') }}" class="text-xs text-blue-600 hover:underline">View all →</a>
            </div>
            <div class="divide-y divide-slate-50">
                @forelse($pending_suits as $suit)
                <div class="px-5 py-3 flex items-center justify-between">
                    <div>
                        <p class="text-sm font-medium text-slate-800">{{ $suit->suit_code }} – {{ $suit->customer->name }}</p>
                        <p class="text-xs text-slate-500">{{ $suit->suit_type }} · {{ $suit->worker?->name ?? 'Unassigned' }}</p>
                    </div>
                    <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $suit->status_badge }}">
                        {{ ucfirst($suit->status) }}
                    </span>
                </div>
                @empty
                <p class="px-5 py-4 text-sm text-slate-400">No active suits.</p>
                @endforelse
            </div>
        </div>

    </div>
</div>
@endsection
