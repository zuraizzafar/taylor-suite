@extends('layouts.app')
@section('title', __('Sales Tax Report'))
@section('page-title', __('Sales Tax Report'))

@section('content')
@php $fmt = fn ($v) => \App\Services\TaxService::fmt($v); @endphp
<div class="max-w-6xl space-y-5 pt-2">

    @include('reports._period-filter', ['route' => 'reports.tax', 'pdfRoute' => 'reports.tax-pdf', 'csvRoute' => 'reports.tax-csv'])

    <div class="grid grid-cols-2 lg:grid-cols-5 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Taxable Value') }}</p>
            <p class="text-xl font-bold text-slate-800 mt-1">Rs {{ $fmt($summary['taxable']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Output Tax') }}</p>
            <p class="text-xl font-bold text-blue-700 mt-1">Rs {{ $fmt($summary['output_tax']) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">
                {{ __('Charged to clients') }}: Rs {{ $fmt($summary['collected']) }}<br>
                {{ __('Borne by us') }}: Rs {{ $fmt($summary['borne']) }}
            </p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Input Tax') }}</p>
            <p class="text-xl font-bold text-purple-700 mt-1">Rs {{ $fmt($summary['input_tax']) }}</p>
            <p class="text-[11px] text-slate-400 mt-1">{{ __('Tax paid on purchases') }}</p>
        </div>
        <div class="lg:col-span-2 {{ $summary['net_payable'] >= 0 ? 'bg-amber-50 border-amber-200' : 'bg-emerald-50 border-emerald-200' }} border rounded-xl p-4">
            <p class="text-xs text-slate-600">{{ $summary['net_payable'] >= 0 ? __('Net Tax Payable') : __('Net Tax Refundable / Carry Forward') }}</p>
            <p class="text-2xl font-bold text-slate-900 mt-1">Rs {{ $fmt(abs($summary['net_payable'])) }}</p>
            <p class="text-[11px] text-slate-500 mt-1">{{ __('Output tax − input tax') }}</p>
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <p class="px-4 py-3 text-sm font-semibold text-slate-700 border-b border-slate-100">{{ __('By Tax Rate') }}</p>
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr>
                    <th class="px-4 py-2 text-left font-medium">{{ __('Rate') }}</th>
                    <th class="px-4 py-2 text-right font-medium">{{ __('Documents') }}</th>
                    <th class="px-4 py-2 text-right font-medium">{{ __('Taxable Value') }}</th>
                    <th class="px-4 py-2 text-right font-medium">{{ __('Tax') }}</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($byRate as $rate => $g)
                    <tr><td class="px-4 py-2">{{ $rate }}%</td><td class="px-4 py-2 text-right">{{ $g['count'] }}</td>
                        <td class="px-4 py-2 text-right">Rs {{ $fmt($g['taxable']) }}</td><td class="px-4 py-2 text-right font-medium">Rs {{ $fmt($g['tax']) }}</td></tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-4 text-center text-slate-400">—</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <p class="px-4 py-3 text-sm font-semibold text-slate-700 border-b border-slate-100">{{ __('By Source') }}</p>
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600"><tr>
                    <th class="px-4 py-2 text-left font-medium">{{ __('Source') }}</th>
                    <th class="px-4 py-2 text-right font-medium">{{ __('Documents') }}</th>
                    <th class="px-4 py-2 text-right font-medium">{{ __('Taxable Value') }}</th>
                    <th class="px-4 py-2 text-right font-medium">{{ __('Tax') }}</th>
                </tr></thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($byType as $type => $g)
                    <tr><td class="px-4 py-2">{{ $type === 'Invoice' ? __('Invoices (Orders)') : __('Fabric Sales') }}</td><td class="px-4 py-2 text-right">{{ $g['count'] }}</td>
                        <td class="px-4 py-2 text-right">Rs {{ $fmt($g['taxable']) }}</td><td class="px-4 py-2 text-right font-medium">Rs {{ $fmt($g['tax']) }}</td></tr>
                    @empty
                    <tr><td colspan="4" class="px-4 py-4 text-center text-slate-400">—</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <p class="px-4 py-3 text-sm font-semibold text-slate-700 border-b border-slate-100">{{ __('Output Tax — Documents') }}</p>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600"><tr>
                <th class="px-4 py-2 text-left font-medium">{{ __('Date') }}</th>
                <th class="px-4 py-2 text-left font-medium">{{ __('Number') }}</th>
                <th class="px-4 py-2 text-left font-medium">{{ __('Buyer') }}</th>
                <th class="px-4 py-2 text-left font-medium">{{ __('NTN') }}</th>
                <th class="px-4 py-2 text-right font-medium">{{ __('Taxable Value') }}</th>
                <th class="px-4 py-2 text-right font-medium">{{ __('Rate') }}</th>
                <th class="px-4 py-2 text-right font-medium">{{ __('Tax') }}</th>
                <th class="px-4 py-2 text-left font-medium">{{ __('Mode') }}</th>
                <th class="px-4 py-2 text-right font-medium">{{ __('Total Payable') }}</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($rows as $r)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2 text-slate-600 text-xs">{{ \Carbon\Carbon::parse($r['date'])->format('d M Y') }}</td>
                    <td class="px-4 py-2 font-mono text-blue-700 font-semibold">{{ $r['number'] }}</td>
                    <td class="px-4 py-2 text-slate-700">{{ $r['buyer'] }}</td>
                    <td class="px-4 py-2 text-slate-500 text-xs">{{ $r['buyer_ntn'] ?: '—' }}</td>
                    <td class="px-4 py-2 text-right">Rs {{ $fmt($r['taxable']) }}</td>
                    <td class="px-4 py-2 text-right">{{ \App\Services\TaxService::rateLabel($r['rate']) }}</td>
                    <td class="px-4 py-2 text-right font-medium">Rs {{ $fmt($r['tax']) }}</td>
                    <td class="px-4 py-2"><span class="text-xs px-2 py-0.5 rounded-full {{ $r['mode'] === 'inclusive' ? 'bg-green-100 text-green-700' : 'bg-amber-100 text-amber-700' }}">{{ $r['mode'] === 'inclusive' ? __('Charged') : __('Not charged') }}</span></td>
                    <td class="px-4 py-2 text-right">Rs {{ $fmt($r['total']) }}</td>
                </tr>
                @empty
                <tr><td colspan="9" class="px-4 py-8 text-center text-slate-400">{{ __('No taxed documents in this period.') }}</td></tr>
                @endforelse
            </tbody>
            @if($rows->isNotEmpty())
            <tfoot class="bg-slate-50 font-semibold"><tr>
                <td colspan="4" class="px-4 py-2 text-right">{{ __('Total') }}</td>
                <td class="px-4 py-2 text-right">Rs {{ $fmt($summary['taxable']) }}</td><td></td>
                <td class="px-4 py-2 text-right">Rs {{ $fmt($summary['output_tax']) }}</td><td></td>
                <td class="px-4 py-2 text-right">Rs {{ $fmt($rows->sum('total')) }}</td>
            </tr></tfoot>
            @endif
        </table>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <p class="px-4 py-3 text-sm font-semibold text-slate-700 border-b border-slate-100">{{ __('Input Tax — Purchases') }}</p>
        <div class="overflow-x-auto">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600"><tr>
                <th class="px-4 py-2 text-left font-medium">{{ __('Date') }}</th>
                <th class="px-4 py-2 text-left font-medium">{{ __('Category') }}</th>
                <th class="px-4 py-2 text-left font-medium">{{ __('Supplier') }}</th>
                <th class="px-4 py-2 text-left font-medium">{{ __('Supplier NTN / STRN') }}</th>
                <th class="px-4 py-2 text-left font-medium">{{ __('Invoice No.') }}</th>
                <th class="px-4 py-2 text-right font-medium">{{ __('Amount') }}</th>
                <th class="px-4 py-2 text-right font-medium">{{ __('Input Tax') }}</th>
            </tr></thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($inputs as $e)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2 text-slate-600 text-xs">{{ $e->date->format('d M Y') }}</td>
                    <td class="px-4 py-2">{{ $e->category }}</td>
                    <td class="px-4 py-2">{{ $e->supplier_name ?: '—' }}</td>
                    <td class="px-4 py-2 text-xs text-slate-500">{{ $e->supplier_ntn ?: '—' }}</td>
                    <td class="px-4 py-2 text-xs text-slate-500">{{ $e->supplier_invoice_no ?: '—' }}</td>
                    <td class="px-4 py-2 text-right">Rs {{ $fmt($e->amount) }}</td>
                    <td class="px-4 py-2 text-right font-medium">Rs {{ $fmt($e->tax_amount) }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">{{ __('No input tax recorded in this period. Add it when recording an expense.') }}</td></tr>
                @endforelse
            </tbody>
            @if($inputs->isNotEmpty())
            <tfoot class="bg-slate-50 font-semibold"><tr>
                <td colspan="5" class="px-4 py-2 text-right">{{ __('Total') }}</td>
                <td class="px-4 py-2 text-right">Rs {{ $fmt($inputs->sum('amount')) }}</td>
                <td class="px-4 py-2 text-right">Rs {{ $fmt($summary['input_tax']) }}</td>
            </tr></tfoot>
            @endif
        </table>
        </div>
    </div>
</div>
@endsection
