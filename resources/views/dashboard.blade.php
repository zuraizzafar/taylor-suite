@extends('layouts.app')

@section('title', 'Dashboard – Suit Tailor')
@section('page-title', 'Dashboard')

@section('content')
<div class="pt-2">

    {{-- Stats cards --}}
    <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4 mb-6">
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

    {{-- Finance Overview --}}
    @if(auth()->user()->isAdmin() || auth()->user()->isBranchManager())
    <div class="mb-6">
        <h3 class="text-sm font-semibold text-slate-600 mb-3 uppercase tracking-wide">💰 Financial Overview</h3>
        <div class="grid grid-cols-2 lg:grid-cols-3 xl:grid-cols-6 gap-4">
            <div class="bg-blue-50 border border-blue-100 rounded-xl p-4">
                <p class="text-xs text-blue-600 font-medium mb-1">Total Revenue</p>
                <p class="text-xl font-bold text-blue-800">Rs {{ number_format($finance['totalRevenue']) }}</p>
                <p class="text-xs text-blue-400 mt-1">All-time billed</p>
            </div>
            <div class="bg-green-50 border border-green-100 rounded-xl p-4">
                <p class="text-xs text-green-600 font-medium mb-1">Total Collected</p>
                <p class="text-xl font-bold text-green-800">Rs {{ number_format($finance['totalCollected']) }}</p>
                <p class="text-xs text-green-400 mt-1">Advance payments</p>
            </div>
            <div class="bg-red-50 border border-red-100 rounded-xl p-4">
                <p class="text-xs text-red-600 font-medium mb-1">Outstanding Dues</p>
                <p class="text-xl font-bold text-red-800">Rs {{ number_format($finance['totalOutstanding']) }}</p>
                <a href="{{ route('reports.pending-balances') }}" class="text-xs text-red-400 mt-1 hover:underline block">View details →</a>
            </div>
            <div class="bg-orange-50 border border-orange-100 rounded-xl p-4">
                <p class="text-xs text-orange-600 font-medium mb-1">Worker Salaries</p>
                <p class="text-xl font-bold text-orange-800">Rs {{ number_format($finance['workerSalaries']) }}</p>
                <a href="{{ route('reports.salary') }}" class="text-xs text-orange-400 mt-1 hover:underline block">Salary report →</a>
            </div>
            <div class="bg-purple-50 border border-purple-100 rounded-xl p-4">
                <p class="text-xs text-purple-600 font-medium mb-1">Total Expenses</p>
                <p class="text-xl font-bold text-purple-800">Rs {{ number_format($finance['totalExpenses']) }}</p>
                <a href="{{ route('expenses.index') }}" class="text-xs text-purple-400 mt-1 hover:underline block">View expenses →</a>
            </div>
            <div class="{{ $finance['netProfit'] >= 0 ? 'bg-emerald-50 border-emerald-100' : 'bg-rose-50 border-rose-100' }} border rounded-xl p-4">
                <p class="text-xs {{ $finance['netProfit'] >= 0 ? 'text-emerald-600' : 'text-rose-600' }} font-medium mb-1">Net Profit</p>
                <p class="text-xl font-bold {{ $finance['netProfit'] >= 0 ? 'text-emerald-800' : 'text-rose-800' }}">
                    Rs {{ number_format(abs($finance['netProfit'])) }}
                    <span class="text-sm font-normal">{{ $finance['netProfit'] >= 0 ? '▲' : '▼' }}</span>
                </p>
                <p class="text-xs {{ $finance['netProfit'] >= 0 ? 'text-emerald-400' : 'text-rose-400' }} mt-1">Collected − Salaries − Expenses</p>
            </div>
        </div>
    </div>
    @endif

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
