@extends('layouts.app')
@section('title', __('Discounts Report'))
@section('page-title', __('Discounts Report'))

@section('content')
@php $fmt = fn ($v) => \App\Services\TaxService::fmt($v); @endphp
<div class="max-w-6xl space-y-5 pt-2">

    @include('reports._period-filter', ['route' => 'reports.discounts', 'pdfRoute' => 'reports.discounts-pdf', 'csvRoute' => 'reports.discounts-csv'])

    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Discount Given on Invoices') }}</p>
            <p class="text-xl font-bold text-rose-600 mt-1">Rs {{ $fmt($summary['invoice_discount']) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">{{ $summary['invoice_count'] }} {{ __('invoices') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Share of Invoiced Sales') }}</p>
            <p class="text-xl font-bold text-slate-800 mt-1">{{ $summary['pct_of_sales'] }}%</p>
            <p class="text-[11px] text-slate-400 mt-1">{{ __('of pre-discount value') }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Discount Quoted') }}</p>
            <p class="text-xl font-bold text-amber-600 mt-1">Rs {{ $fmt($summary['quotation_discount']) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">{{ $summary['quotation_count'] }} {{ __('quotations') }}</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600"><tr>
                <th class="px-4 py-3 text-left font-medium">{{ __('Date') }}</th>
                <th class="px-4 py-3 text-left font-medium">{{ __('Type') }}</th>
                <th class="px-4 py-3 text-left font-medium">{{ __('Number') }}</th>
                <th class="px-4 py-3 text-left font-medium">{{ __('Customer') }}</th>
                <th class="px-4 py-3 text-right font-medium">{{ __('Subtotal') }}</th>
                <th class="px-4 py-3 text-right font-medium">{{ __('Discount') }}</th>
                <th class="px-4 py-3 text-right font-medium">{{ __('Total Payable') }}</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($rows as $r)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2 text-slate-600 text-xs">{{ \Carbon\Carbon::parse($r['date'])->format('d M Y') }}</td>
                    <td class="px-4 py-2"><span class="text-xs px-2 py-0.5 rounded-full {{ $r['type'] === 'Invoice' ? 'bg-blue-100 text-blue-700' : 'bg-amber-100 text-amber-700' }}">{{ $r['type'] === 'Invoice' ? __('Invoice') : __('Quotation') }}</span></td>
                    <td class="px-4 py-2 font-mono text-blue-700 font-semibold">{{ $r['number'] }}</td>
                    <td class="px-4 py-2 text-slate-700">{{ $r['customer'] }}</td>
                    <td class="px-4 py-2 text-right">Rs {{ $fmt($r['subtotal']) }}</td>
                    <td class="px-4 py-2 text-right text-rose-600 font-medium">
                        − Rs {{ $fmt($r['discount']) }}
                        @if($r['discount_type'] === 'percent')<span class="text-xs text-slate-400">({{ \App\Services\TaxService::rateLabel($r['discount_value']) }})</span>@endif
                    </td>
                    <td class="px-4 py-2 text-right">Rs {{ $fmt($r['total']) }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">{{ __('No discounts in this period.') }}</td></tr>
                @endforelse
            </tbody>
        </table>
        </div>
    </div>
</div>
@endsection
