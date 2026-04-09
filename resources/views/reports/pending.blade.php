@extends('layouts.app')
@section('title', 'Pending Orders')
@section('page-title', 'Pending Orders')

@section('content')
<div class="pt-2">
    <form method="GET" action="{{ route('reports.pending') }}" class="flex gap-2 mb-5">
        <select name="status"
            class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">All Active Statuses</option>
            @foreach(['pending','cutting','stitching','ready'] as $s)
            <option value="{{ $s }}" {{ $status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
            @endforeach
        </select>
        <button class="bg-slate-700 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-700">Active Suits</h3>
            <span class="text-xs text-slate-500">{{ $suits->count() }} suit(s)</span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-2 text-left font-medium">Code</th>
                    <th class="px-4 py-2 text-left font-medium">Customer</th>
                    <th class="px-4 py-2 text-left font-medium">Type</th>
                    <th class="px-4 py-2 text-left font-medium">Worker</th>
                    <th class="px-4 py-2 text-left font-medium">Status</th>
                    <th class="px-4 py-2 text-left font-medium">Order</th>
                    <th class="px-4 py-2 text-left font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($suits as $suit)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2 font-mono font-semibold text-blue-700">{{ $suit->suit_code }}</td>
                    <td class="px-4 py-2 font-medium text-slate-800">{{ $suit->customer->name }}</td>
                    <td class="px-4 py-2 text-slate-600">{{ $suit->suit_type }}</td>
                    <td class="px-4 py-2 text-slate-600">{{ $suit->worker?->name ?? '—' }}</td>
                    <td class="px-4 py-2">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $suit->status_badge }}">
                            {{ ucfirst($suit->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-slate-600">
                        @if($suit->order)
                        <a href="{{ route('orders.show', $suit->order) }}"
                           class="text-blue-600 hover:underline">{{ $suit->order->delivery_date->format('d M') }}</a>
                        @else —
                        @endif
                    </td>
                    <td class="px-4 py-2">
                        <a href="{{ route('suits.show', $suit) }}"
                           class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No pending suits.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
