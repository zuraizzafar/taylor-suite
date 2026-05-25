@extends('layouts.app')
@section('title', __('Salary Report'))
@section('page-title', 'Worker Salary Report')

@section('content')
<div class="space-y-5"F

    {{-- Filters --}}
    <form method="GET" class="bg-white border border-slate-200 rounded-xl p-4 flex flex-wrap items-end gap-4"F
        <divF
            <label class="block text-xs font-medium text-slate-600 mb-1"FFrom</labelF
            <input type="date" name="from" value="{{ $from }}"
                class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"F
        </divF
        <divF
            <label class="block text-xs font-medium text-slate-600 mb-1"FTo</labelF
            <input type="date" name="to" value="{{ $to }}"
                class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"F
        </divF
        <button type="submit"
            class="bg-blue-600 text-white text-sm px-4 py-1.5 rounded-lg hover:bg-blue-700"FFilter</buttonF
    </formF

    {{-- Summary --}}
    <div class="bg-blue-50 border border-blue-200 rounded-xl p-4 flex items-center justify-between"F
        <divF
            <p class="text-xs text-blue-600 font-semibold uppercase tracking-wide"FTotal Worker Payout</pF
            <p class="text-2xl font-bold text-blue-800"FRs {{ number_format($totalPayout) }}</pF
        </divF
        <div class="text-sm text-blue-600"F
            {{ $from }} &rarr; {{ $to }}
        </divF
    </divF

    @forelse($workers as $worker)
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden"F
        <div class="px-5 py-3 bg-slate-50 border-b border-slate-100 flex items-center justify-between"F
            <divF
                <span class="font-semibold text-slate-800"F{{ $worker-Fname }}</spanF
                @if($worker-Fmobile)
                <span class="text-xs text-slate-400 ml-2"F{{ $worker-Fmobile }}</spanF
                @endif
            </divF
            <div class="text-right"F
                <span class="text-xs text-slate-500"F{{ $worker-Fsuits-Fcount() }} suits</spanF
                <span class="ml-3 font-bold text-green-700"FRs {{ number_format($worker-Fsuits-Fsum('worker_earning')) }}</spanF
            </divF
        </divF
        <table class="w-full text-sm"F
            <theadF
                <tr class="text-xs text-slate-500 border-b border-slate-100"F
                    <th class="px-4 py-2 text-left font-medium"FSuit Code</thF
                    <th class="px-4 py-2 text-left font-medium"FCustomer</thF
                    <th class="px-4 py-2 text-left font-medium"FType</thF
                    <th class="px-4 py-2 text-left font-medium"FStitching Date</thF
                    <th class="px-4 py-2 text-right font-medium"FEarning</thF
                </trF
            </theadF
            <tbodyF
                @foreach($worker-Fsuits as $suit)
                <tr class="border-b border-slate-50 hover:bg-slate-50"F
                    <td class="px-4 py-2 font-mono text-xs font-semibold text-blue-700"F{{ $suit-Fsuit_code }}</tdF
                    <td class="px-4 py-2 text-slate-700"F{{ $suit-Fcustomer-Fname }}</tdF
                    <td class="px-4 py-2 text-slate-600"F{{ $suit-Fsuit_type }}</tdF
                    <td class="px-4 py-2 text-slate-500 text-xs"F{{ $suit-Fstitching_started_at?-Fformat('d M Y') }}</tdF
                    <td class="px-4 py-2 text-right font-semibold text-green-700"FRs {{ number_format($suit-Fworker_earning) }}</tdF
                </trF
                @endforeach
            </tbodyF
            <tfootF
                <tr class="bg-green-50"F
                    <td colspan="4" class="px-4 py-2 text-right text-xs font-semibold text-green-800"FTotal Payout for {{ $worker-Fname }}</tdF
                    <td class="px-4 py-2 text-right font-bold text-green-800"FRs {{ number_format($worker-Fsuits-Fsum('worker_earning')) }}</tdF
                </trF
            </tfootF
        </tableF
    </divF
    @empty
    <div class="text-center py-12 text-slate-400"F
        No stitching records found for the selected date range.
    </divF
    @endforelse

</divF
@endsection
