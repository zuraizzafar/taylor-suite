@extends('layouts.app')
@section('title', __('Fabric Profit Report'))
@section('page-title', __('Fabric Profit Report'))

@section('content')
<div class="max-w-6xl space-y-5 pt-2">

    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4">
        <form method="GET" action="{{ route('reports.fabric-profit') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('From:') }}</label>
                <input type="date" name="from" value="{{ $from }}"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('To:') }}</label>
                <input type="date" name="to" value="{{ $to }}"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button class="bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-4 py-1.5 rounded-lg">{{ __('Filter') }}</button>
            <a href="{{ route('reports.export-csv', array_merge(['report' => 'fabric-profit'], request()->all())) }}" 
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-1.5 rounded-lg flex items-center gap-1 font-semibold">
                📥 {{ __('Export Excel') }}
            </a>
            <a href="{{ route('reports.fabric-profit-pdf', request()->all()) }}" target="_blank"
               class="bg-slate-700 hover:bg-slate-800 text-white text-sm px-4 py-1.5 rounded-lg flex items-center gap-1 font-semibold">
                📄 {{ __('Print PDF') }}
            </a>
            <div class="flex gap-1">
                @foreach([
                    'Today' => 'today',
                    'This Week' => 'week',
                    'This Month' => 'month',
                    'Last Month' => 'last_month',
                ] as $label => $preset_val)
                <a href="{{ route('reports.fabric-profit', ['preset' => $preset_val]) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ ($preset ?? '') === $preset_val ? 'bg-blue-600 text-white border-blue-600' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                    {{ $label }}
                </a>
                @endforeach
            </div>
        </form>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Cost') }}</p>
            <p class="text-xl font-bold text-slate-800 mt-1">Rs {{ number_format($cost) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Sale') }}</p>
            <p class="text-xl font-bold text-slate-800 mt-1">Rs {{ number_format($sale) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Profit') }}</p>
            <p class="text-xl font-bold text-green-600 mt-1">Rs {{ number_format($profit) }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Invoice') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Customer') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Fabric') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Meter') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Cost') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Sale') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Profit') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($sales as $s)
                @php
                    $lineCost = $s->meter * ($s->fabric?->cost_price ?? 0);
                    $lineProfit = $s->total_amount - $lineCost;
                @endphp
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-600 text-xs">{{ $s->created_at->format('d M Y') }}</td>
                    <td class="px-4 py-3 font-mono text-blue-700 font-semibold">{{ $s->sale_code }}</td>
                    <td class="px-4 py-3 text-slate-700">{{ $s->customer_name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $s->fabric?->roll_number ?? '—' }}</td>
                    <td class="px-4 py-3">{{ number_format($s->meter, 2) }}m</td>
                    <td class="px-4 py-3">Rs {{ number_format($lineCost) }}</td>
                    <td class="px-4 py-3">Rs {{ number_format($s->total_amount) }}</td>
                    <td class="px-4 py-3 text-green-600 font-medium">Rs {{ number_format($lineProfit) }}</td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">{{ __('No fabric sales in this period.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
