@extends('layouts.app')
@section('title', 'Delivered Orders')
@section('page-title', 'Delivered Orders')

@section('content')
<div class="pt-2">
    <form method="GET" action="{{ route('reports.delivered') }}" class="flex gap-2 mb-5">
        <div>
            <label class="text-xs text-slate-500 block mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="text-xs text-slate-500 block mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div class="flex items-end">
            <button class="bg-slate-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-slate-800">Filter</button>
        </div>
    </form>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-700">Delivered Suits</h3>
            <span class="text-xs text-slate-500">{{ $suits->count() }} suit(s)</span>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-2 text-left font-medium">Code</th>
                    <th class="px-4 py-2 text-left font-medium">Customer</th>
                    <th class="px-4 py-2 text-left font-medium">Type</th>
                    <th class="px-4 py-2 text-left font-medium">Delivered</th>
                    <th class="px-4 py-2 text-left font-medium">Worker</th>
                    <th class="px-4 py-2 text-left font-medium">Action</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($suits as $suit)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2 font-mono font-semibold text-blue-700">{{ $suit->suit_code }}</td>
                    <td class="px-4 py-2 font-medium text-slate-800">{{ $suit->customer->name }}</td>
                    <td class="px-4 py-2 text-slate-600">{{ $suit->suit_type }}</td>
                    <td class="px-4 py-2 text-slate-600">{{ $suit->delivered_at?->format('d M Y') }}</td>
                    <td class="px-4 py-2 text-slate-600">{{ $suit->worker?->name ?? '—' }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('suits.show', $suit) }}"
                           class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No delivered suits in this period.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
