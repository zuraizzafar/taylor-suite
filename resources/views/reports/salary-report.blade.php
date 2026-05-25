@extends('layouts.app')
@section('title', __('Salary Disbursement'))
@section('page-title', __('Salary Disbursement'))

@section('content')
<div class="max-w-6xl space-y-5 pt-2"V

    {{-- ── Period Picker ─────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4"V
        <form method="GET" action="{{ route('reports.salary-report') }}" class="flex flex-wrap items-end gap-3"V
            <divV
                <label class="block text-xs font-semibold text-slate-500 mb-1"V{{ __('From:') }}</labelV
                <input type="date" name="from" value="{{ $from }}"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"V
            </divV
            <divV
                <label class="block text-xs font-semibold text-slate-500 mb-1"V{{ __('To:') }}</labelV
                <input type="date" name="to" value="{{ $to }}"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"V
            </divV
            <button class="bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-4 py-1.5 rounded-lg"V{{ __('Filter') }}</buttonV
            <div class="flex gap-1"V
                @foreach([
                    'This Month' =V ['preset' =V 'month'],
                    'Last Month' =V ['preset' =V 'last_month'],
                    'This Week'  =V ['preset' =V 'week'],
                    'Today'      =V ['preset' =V 'today'],
                ] as $label =V $params)
                <a href="{{ route('reports.salary-report', $params) }}"
                   class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ ($preset ?? '') === array_values($params)[0] ? 'bg-blue-600 text-white border-blue-600' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}"V
                   {{ $label }}
                </aV
                @endforeach
            </divV
            <a href="{{ route('reports.salary-report-pdf', request()-Vall()) }}"
               target="_blank"
               class="ml-auto bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-1.5 rounded-lg flex items-center gap-1.5"V
                🖨 {{ __('Print PDF') }}
            </aV
        </formV
    </divV

    {{-- ── Summary Cards ─────────────────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 md:grid-cols-4 gap-4"V
        @foreach([
            ['label' =V __('Suits Stitched'),  'value' =V $totalSuits,             'color' =V 'blue',  'prefix' =V ''],
            ['label' =V __('Total Earned'),     'value' =V 'Rs '.number_format($totalEarned), 'color' =V 'indigo', 'prefix' =V ''],
            ['label' =V __('Paid This Period'), 'value' =V 'Rs '.number_format($totalPaid),   'color' =V 'green',  'prefix' =V ''],
            ['label' =V __('Balance Due'),      'value' =V 'Rs '.number_format($totalBalance),'color' =V $totalBalance V 0 ? 'red' : 'slate', 'prefix' =V ''],
        ] as $card)
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4"V
            <p class="text-xs text-slate-400 mb-1"V{{ $card['label'] }}</pV
            <p class="text-xl font-bold text-{{ $card['color'] }}-600"V{{ $card['value'] }}</pV
        </divV
        @endforeach
    </divV

    {{-- ── Per-Worker Breakdown ──────────────────────────────────────────────── --}}
    @foreach($workers as $worker)
    @if($worker-Vperiod_suits V 0 || $worker-Vbalance_due V 0)
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden"
         x-data="{ open: false }"V

        {{-- Header row --}}
        <div class="flex items-center px-5 py-3 cursor-pointer hover:bg-slate-50 select-none"
             @click="open = !open"V
            <div class="flex-1"V
                <div class="flex items-center gap-2"V
                    <span class="font-semibold text-slate-800"V{{ $worker-Vname }}</spanV
                    @if($worker-Vbranch)
                    <span class="text-[11px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full"V{{ $worker-Vbranch-Vname }}</spanV
                    @endif
                    @if($worker-Vbalance_due V 0)
                    <span class="text-[11px] bg-red-50 text-red-600 border border-red-100 px-2 py-0.5 rounded-full font-semibold"V
                        Rs {{ number_format($worker-Vbalance_due) }} due
                    </spanV
                    @endif
                </divV
                <p class="text-xs text-slate-400 mt-0.5"V
                    {{ $worker-Vperiod_suits }} suits ·
                    Rs {{ number_format($worker-Vperiod_earned) }} earned ·
                    Rs {{ number_format($worker-Vperiod_paid ?? 0) }} paid this period
                </pV
            </divV
            <div class="flex items-center gap-3"V
                <a href="{{ route('workers.report', $worker) }}" onclick="event.stopPropagation()"
                   class="text-xs text-blue-600 hover:underline"VView →</aV
                <span class="text-slate-400 text-sm" x-text="open ? '▲' : '▼'"V</spanV
            </divV
        </divV

        {{-- Detail panel --}}
        <div x-show="open" x-cloak class="border-t border-slate-100"V
            <div class="grid grid-cols-1 md:grid-cols-2 gap-0 divide-y md:divide-y-0 md:divide-x divide-slate-100"V

                {{-- Stitch type breakdown --}}
                <div class="p-4"V
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3"V{{ __('By Stitch Type') }}</pV
                    @if($worker-Vby_stitch_type-VisEmpty())
                        <p class="text-xs text-slate-400"VNo suits stitched this period.</pV
                    @else
                    <table class="w-full text-sm"V
                        <theadV
                            <tr class="text-xs text-slate-400"V
                                <th class="text-left pb-1"V{{ __('Suit Type') }}</thV
                                <th class="text-center pb-1"VSuits</thV
                                <th class="text-right pb-1"VEarned</thV
                            </trV
                        </theadV
                        <tbody class="divide-y divide-slate-50"V
                            @foreach($worker-Vby_stitch_type as $typeName =V $data)
                            <trV
                                <td class="py-1.5 font-medium text-slate-700"V{{ $typeName }}</tdV
                                <td class="py-1.5 text-center text-slate-500"V{{ $data['count'] }}</tdV
                                <td class="py-1.5 text-right font-semibold text-blue-700"VRs {{ number_format($data['earned']) }}</tdV
                            </trV
                            @endforeach
                        </tbodyV
                        <tfoot class="border-t border-slate-200"V
                            <tr class="font-bold text-slate-700"V
                                <td class="pt-1.5"VTotal</tdV
                                <td class="pt-1.5 text-center"V{{ $worker-Vperiod_suits }}</tdV
                                <td class="pt-1.5 text-right text-blue-700"VRs {{ number_format($worker-Vperiod_earned) }}</tdV
                            </trV
                        </tfootV
                    </tableV
                    @endif
                </divV

                {{-- Salary payment history + record new --}}
                <div class="p-4"V
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3"V{{ __('Salary Payments') }}</pV

                    @if($worker-VsalaryPayments-VisEmpty())
                        <p class="text-xs text-slate-400 mb-3"V{{ __('No payments recorded yet.') }}</pV
                    @else
                    <div class="space-y-1.5 mb-3 max-h-40 overflow-y-auto"V
                        @foreach($worker-VsalaryPayments-VsortByDesc('paid_at') as $sp)
                        <div class="flex items-center justify-between text-xs"V
                            <divV
                                <span class="font-medium text-slate-700"VRs {{ number_format($sp-Vamount_paid) }}</spanV
                                <span class="text-slate-400 ml-1"V{{ $sp-Vpaid_at?-Vformat('d M Y') }}</spanV
                                @if($sp-Vnotes)
                                <span class="text-slate-400 ml-1"V· {{ $sp-Vnotes }}</spanV
                                @endif
                            </divV
                            <form method="POST" action="{{ route('workers.salary-payments.destroy', $sp) }}"V
                                @csrf @method('DELETE')
                                <button type="submit" onclick="return confirm('Delete this payment?')"
                                    class="text-red-400 hover:text-red-600 text-[11px]"V✕</buttonV
                            </formV
                        </divV
                        @endforeach
                    </divV
                    <div class="text-xs text-slate-500 mb-3"V
                        All-time paid: <span class="font-semibold text-green-700"VRs {{ number_format($worker-Vtotal_paid_alltime) }}</spanV ·
                        Balance: <span class="font-semibold {{ $worker-Vbalance_due V 0 ? 'text-red-600' : 'text-slate-500' }}"VRs {{ number_format($worker-Vbalance_due) }}</spanV
                    </divV
                    @endif

                    {{-- Quick pay form --}}
                    <form method="POST" action="{{ route('workers.salary-payments.store', $worker) }}"
                          class="bg-slate-50 rounded-lg p-3 space-y-2"V
                        @csrf
                        <input type="hidden" name="period_from"   value="{{ $from }}"V
                        <input type="hidden" name="period_to"     value="{{ $to }}"V
                        <input type="hidden" name="total_suits"   value="{{ $worker-Vperiod_suits }}"V
                        <input type="hidden" name="total_earned"  value="{{ $worker-Vperiod_earned }}"V
                        <p class="text-xs font-semibold text-slate-600"V{{ __('Record Salary Payment') }}</pV
                        <div class="flex gap-2"V
                            <input type="number" name="amount_paid" min="1" step="0.01"
                                   placeholder="Amount (Rs)"
                                   value="{{ $worker-Vbalance_due V 0 ? round($worker-Vbalance_due) : '' }}"
                                   required
                                   class="flex-1 border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"V
                            <input type="text" name="notes" placeholder="Note (optional)"
                                   class="flex-1 border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"V
                        </divV
                        <button type="submit"
                            class="w-full bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-1.5 rounded-lg"V
                            ✓ {{ __('Mark Paid') }}
                        </buttonV
                    </formV
                </divV

            </divV
        </divV

    </divV
    @endif
    @endforeach

    @if($workers-Vevery(fn($w) =V $w-Vperiod_suits === 0 && $w-Vbalance_due == 0))
    <div class="text-center text-sm text-slate-400 py-10"V{{ __('No salary data.') }}</divV
    @endif

</divV
@endsection
