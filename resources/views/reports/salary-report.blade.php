@extends('layouts.app')
@section('title', __('Salary Disbursement'))
@section('page-title', __('Salary Disbursement'))

@section('content')
<div class="max-w-6xl space-y-5 pt-2">

    {{-- ── Period Picker ─────────────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4">
        <form method="GET" action="{{ route('reports.salary-report') }}" class="flex flex-wrap items-end gap-3">
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
            <div class="flex gap-1">
                @foreach([
                    'This Month' => ['preset' => 'month'],
                    'Last Month' => ['preset' => 'last_month'],
                    'This Week'  => ['preset' => 'week'],
                    'Today'      => ['preset' => 'today'],
                @extends('layouts.app')
                @section('title', __('Salary Disbursement'))
                @section('page-title', __('Salary Disbursement'))

                @section('content')
                <div class="max-w-6xl space-y-5 pt-2">

                    {{-- ── Period Picker ─────────────────────────────────────────────────────── --}}
                    <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4">
                        <form method="GET" action="{{ route('reports.salary-report') }}" class="flex flex-wrap items-end gap-3">
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
                            <div class="flex gap-1">
                                @foreach([
                                    'This Month' => ['preset' => 'month'],
                                    'Last Month' => ['preset' => 'last_month'],
                                    'This Week' => ['preset' => 'week'],
                                    'Today' => ['preset' => 'today'],
                                ] as $label => $params)
                                <a href="{{ route('reports.salary-report', $params) }}"
                                   class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ ($preset ?? '') === array_values($params)[0] ? 'bg-blue-600 text-white border-blue-600' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                                    {{ $label }}
                                </a>
                                @endforeach
                            </div>
                            <a href="{{ route('reports.salary-report-pdf', request()->all()) }}"
                               target="_blank"
                               class="ml-auto bg-red-600 hover:bg-red-700 text-white text-sm font-medium px-4 py-1.5 rounded-lg flex items-center gap-1.5">
                                🖨 {{ __('Print PDF') }}
                            </a>
                        </form>
                    </div>

                    {{-- ── Summary Cards ─────────────────────────────────────────────────────── --}}
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
                        @foreach([
                            ['label' => __('Suits Stitched'), 'value' => $totalSuits, 'color' => 'blue'],
                            ['label' => __('Total Earned'), 'value' => 'Rs '.number_format($totalEarned), 'color' => 'indigo'],
                            ['label' => __('Paid This Period'), 'value' => 'Rs '.number_format($totalPaid), 'color' => 'green'],
                            ['label' => __('Balance Due'), 'value' => 'Rs '.number_format($totalBalance), 'color' => $totalBalance > 0 ? 'red' : 'slate'],
                        ] as $card)
                        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4">
                            <p class="text-xs text-slate-400 mb-1">{{ $card['label'] }}</p>
                            <p class="text-xl font-bold text-{{ $card['color'] }}-600">{{ $card['value'] }}</p>
                        </div>
                        @endforeach
                    </div>

                    {{-- ── Per-Worker Breakdown ──────────────────────────────────────────────── --}}
                    @foreach($workers as $worker)
                    @if($worker->period_suits > 0 || $worker->balance_due > 0)
                    <div class="bg-white rounded-xl border border-slate-100 shadow-sm overflow-hidden"
                         x-data="{ open: false }">

                        {{-- Header row --}}
                        <div class="flex items-center px-5 py-3 cursor-pointer hover:bg-slate-50 select-none"
                             @click="open = !open">
                            <div class="flex-1">
                                <div class="flex items-center gap-2">
                                    <span class="font-semibold text-slate-800">{{ $worker->name }}</span>
                                    @if($worker->branch)
                                    <span class="text-[11px] bg-slate-100 text-slate-500 px-2 py-0.5 rounded-full">{{ $worker->branch->name }}</span>
                                    @endif
                                    @if($worker->balance_due > 0)
                                    <span class="text-[11px] bg-red-50 text-red-600 border border-red-100 px-2 py-0.5 rounded-full font-semibold">
                                        Rs {{ number_format($worker->balance_due) }} due
                                    </span>
                                    @endif
                                </div>
                                <p class="text-xs text-slate-400 mt-0.5">
                                    {{ $worker->period_suits }} suits ·
                                    Rs {{ number_format($worker->period_earned) }} earned ·
                                    Rs {{ number_format($worker->period_paid ?? 0) }} paid this period
                                </p>
                            </div>
                            <div class="flex items-center gap-3">
                                <a href="{{ route('workers.report', $worker) }}" onclick="event.stopPropagation()"
                                   class="text-xs text-blue-600 hover:underline">View →</a>
                                <span class="text-slate-400 text-sm" x-text="open ? '▲' : '▼'"></span>
                            </div>
                        </div>

                        {{-- Detail panel --}}
                        <div x-show="open" x-cloak class="border-t border-slate-100">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-0 divide-y md:divide-y-0 md:divide-x divide-slate-100">

                                {{-- Stitch type breakdown --}}
                                <div class="p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">{{ __('By Stitch Type') }}</p>
                                    @if($worker->by_stitch_type->isEmpty())
                                        <p class="text-xs text-slate-400">No suits stitched this period.</p>
                                    @else
                                    <table class="w-full text-sm">
                                        <thead>
                                            <tr class="text-xs text-slate-400">
                                                <th class="text-left pb-1">{{ __('Suit Type') }}</th>
                                                <th class="text-center pb-1">Suits</th>
                                                <th class="text-right pb-1">Earned</th>
                                            </tr>
                                        </thead>
                                        <tbody class="divide-y divide-slate-50">
                                            @foreach($worker->by_stitch_type as $typeName => $data)
                                            <tr>
                                                <td class="py-1.5 font-medium text-slate-700">{{ $typeName }}</td>
                                                <td class="py-1.5 text-center text-slate-500">{{ $data['count'] }}</td>
                                                <td class="py-1.5 text-right font-semibold text-blue-700">Rs {{ number_format($data['earned']) }}</td>
                                            </tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot class="border-t border-slate-200">
                                            <tr class="font-bold text-slate-700">
                                                <td class="pt-1.5">Total</td>
                                                <td class="pt-1.5 text-center">{{ $worker->period_suits }}</td>
                                                <td class="pt-1.5 text-right text-blue-700">Rs {{ number_format($worker->period_earned) }}</td>
                                            </tr>
                                        </tfoot>
                                    </table>
                                    @endif
                                </div>

                                {{-- Salary payment history + record new --}}
                                <div class="p-4">
                                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-3">{{ __('Salary Payments') }}</p>

                                    @if($worker->salaryPayments->isEmpty())
                                        <p class="text-xs text-slate-400 mb-3">{{ __('No payments recorded yet.') }}</p>
                                    @else
                                    <div class="space-y-1.5 mb-3 max-h-40 overflow-y-auto">
                                        @foreach($worker->salaryPayments->sortByDesc('paid_at') as $sp)
                                        <div class="flex items-center justify-between text-xs">
                                            <div>
                                                <span class="font-medium text-slate-700">Rs {{ number_format($sp->amount_paid) }}</span>
                                                <span class="text-slate-400 ml-1">{{ $sp->paid_at?->format('d M Y') }}</span>
                                                @if($sp->notes)
                                                <span class="text-slate-400 ml-1">· {{ $sp->notes }}</span>
                                                @endif
                                            </div>
                                            <form method="POST" action="{{ route('workers.salary-payments.destroy', $sp) }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" onclick="return confirm('Delete this payment?')"
                                                    class="text-red-400 hover:text-red-600 text-[11px]">✕</button>
                                            </form>
                                        </div>
                                        @endforeach
                                    </div>
                                    <div class="text-xs text-slate-500 mb-3">
                                        All-time paid: <span class="font-semibold text-green-700">Rs {{ number_format($worker->total_paid_alltime) }}</span> ·
                                        Balance: <span class="font-semibold {{ $worker->balance_due > 0 ? 'text-red-600' : 'text-slate-500' }}">Rs {{ number_format($worker->balance_due) }}</span>
                                    </div>
                                    @endif

                                    {{-- Quick pay form --}}
                                    <form method="POST" action="{{ route('workers.salary-payments.store', $worker) }}"
                                          class="bg-slate-50 rounded-lg p-3 space-y-2">
                                        @csrf
                                        <input type="hidden" name="period_from" value="{{ $from }}">
                                        <input type="hidden" name="period_to" value="{{ $to }}">
                                        <input type="hidden" name="total_suits" value="{{ $worker->period_suits }}">
                                        <input type="hidden" name="total_earned" value="{{ $worker->period_earned }}">
                                        <p class="text-xs font-semibold text-slate-600">{{ __('Record Salary Payment') }}</p>
                                        <div class="flex gap-2">
                                            <input type="number" name="amount_paid" min="1" step="0.01"
                                                   placeholder="Amount (Rs)"
                                                   value="{{ $worker->balance_due > 0 ? round($worker->balance_due) : '' }}"
                                                   required
                                                   class="flex-1 border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                            <input type="text" name="notes" placeholder="Note (optional)"
                                                   class="flex-1 border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                        </div>
                                        <button type="submit"
                                            class="w-full bg-green-600 hover:bg-green-700 text-white text-xs font-semibold py-1.5 rounded-lg">
                                            ✓ {{ __('Mark Paid') }}
                                        </button>
                                    </form>
                                </div>

                            </div>
                        </div>

                    </div>
                    @endif
                    @endforeach

                    @if($workers->every(fn($worker) => $worker->period_suits === 0 && $worker->balance_due == 0))
                    <div class="text-center text-sm text-slate-400 py-10">{{ __('No salary data.') }}</div>
                    @endif

                </div>
                @endsection
