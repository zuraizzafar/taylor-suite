@extends('layouts.app')
@section('title', 'Salary Report')
@section('page-title', 'Worker Salary Report')

@section('content')
<div class="space-y-5">

    {{-- Filters --}}
    <form method="GET" class="bg-white border border-slate-200 rounded-xl p-4 flex flex-wrap items-end gap-4">
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">From</label>
            <input type="date" name="from" value="{{ $from }}"
                class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">To</label>
            <input type="date" name="to" value="{{ $to }}"
                class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button type="submit"
            class="bg-blue-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-blue-700">Filter</button>
    </form>

    {{-- Summary --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center justify-between">
        <div>
            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide">Total Worker Payout</p>
            <p class="text-2xl font-bold text-blue-800">Rs {{ number_format($totalPayout) }}</p>
        </div>
        <div class="text-sm text-blue-600">
            {{ $from }} &rarr; {{ $to }}
        </div>
    </div>

    @forelse($workers as $worker)
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <div class="px-5 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between">
            <div>
                <span class="font-semibold text-slate-800">{{ $worker->name }}</span>
                @if($worker->mobile)
                <span class="text-xs text-slate-400 ml-2">{{ $worker->mobile }}</span>
                @endif
            </div>
            <div class="text-right">
                <span class="text-xs text-slate-500">{{ $worker->suits->count() }} suits</span>
                <span class="ml-3 font-bold text-green-700">Rs {{ number_format($worker->suits->sum('worker_earning')) }}</span>
            </div>
        </div>
        <table class="w-full text-sm">
            <thead>
                <tr class="text-xs text-slate-500 border-b border-slate-100">
                    <th class="px-4 py-2 text-left font-medium">Suit Code</th>
                    <th class="px-4 py-2 text-left font-medium">Customer</th>
                    <th class="px-4 py-2 text-left font-medium">Type</th>
                    <th class="px-4 py-2 text-left font-medium">Stitching Date</th>
                    <th class="px-4 py-2 text-right font-medium">Earning</th>
                </tr>
            </thead>
            <tbody>
                @foreach($worker->suits as $suit)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="px-4 py-2 font-mono text-xs font-semibold text-blue-700">{{ $suit->suit_code }}</td>
                    <td class="px-4 py-2 text-slate-700">{{ $suit->customer->name }}</td>
                    <td class="px-4 py-2 text-slate-600">{{ $suit->suit_type }}</td>
                    <td class="px-4 py-2 text-slate-500 text-xs">{{ $suit->stitching_started_at?->format('d M Y') }}</td>
                    <td class="px-4 py-2 text-right font-semibold text-green-700">Rs {{ number_format($suit->worker_earning) }}</td>
                </tr>
                @endforeach
            </tbody>
            <tfoot>
                <tr class="bg-green-50">
                    <td colspan="4" class="px-4 py-2 text-right text-xs font-semibold text-green-800">Total Payout for {{ $worker->name }}</td>
                    <td class="px-4 py-2 text-right font-bold text-green-800">Rs {{ number_format($worker->suits->sum('worker_earning')) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>
    @empty
    <div class="text-center py-12 text-slate-400">
        No stitching records found for the selected date range.
    </div>
    @endforelse

</div>
@endsection
