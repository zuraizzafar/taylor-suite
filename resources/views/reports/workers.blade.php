@extends('layouts.app')
@section('title', __('Workers Report'))
@section('page-title', __('Workers Report'))

@section('content')
<div class="pt-2 max-w-5xl space-y-5">

    {{-- ── Filters ──────────────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <form method="GET" action="{{ route('reports.workers') }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('From:') }}</label>
                <input type="date" name="from" value="{{ $from }}"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('To:') }}</label>
                <input type="date" name="to" value="{{ $to }}"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                class="bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-4 py-1.5 rounded-lg">
                {{ __('Filter') }}
            </button>
            <a href="{{ route('reports.export-csv', array_merge(['report' => 'workers'], request()->all())) }}" 
               class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-1.5 rounded-lg flex items-center gap-1 font-semibold">
                📥 {{ __('Export Excel') }}
            </a>
            <a href="{{ route('reports.workers-pdf', request()->all()) }}" target="_blank"
               class="bg-slate-700 hover:bg-slate-800 text-white text-sm px-4 py-1.5 rounded-lg flex items-center gap-1 font-semibold">
                📄 {{ __('Print PDF') }}
            </a>
            <div class="flex gap-1 ml-2">
                @foreach(['This Month' => [today()->startOfMonth()->toDateString(), today()->toDateString()], 'Last Month' => [today()->subMonth()->startOfMonth()->toDateString(), today()->subMonth()->endOfMonth()->toDateString()]] as $label => $range)
                <a href="{{ route('reports.workers', ['from' => $range[0], 'to' => $range[1]]) }}"
                    class="px-3 py-1.5 rounded-lg text-xs font-medium border bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100">
                    {{ __($label) }}
                </a>
                @endforeach
            </div>
        </form>
    </div>

    {{-- ── Summary Bar ───────────────────────────────────────────── --}}
    <div class="grid grid-cols-3 gap-4">
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 text-center">
            <p class="text-xs text-slate-500 mb-1">{{ __('Total Suits Stitched') }}</p>
            <p class="text-3xl font-bold text-slate-800">{{ $totalSuits }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 text-center">
            <p class="text-xs text-slate-500 mb-1">{{ __('Total Earned') }}</p>
            <p class="text-2xl font-bold text-blue-600">Rs {{ number_format($totalEarned) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 text-center">
            <p class="text-xs text-slate-500 mb-1">{{ __('Total Paid Out') }}</p>
            <p class="text-2xl font-bold text-green-600">Rs {{ number_format($totalPaid) }}</p>
        </div>
    </div>

    {{-- ── Leaderboard Table ─────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h2 class="text-sm font-semibold text-slate-700">{{ __('Worker Leaderboard') }}</h2>
            <p class="text-xs text-slate-400 mt-0.5">{{ $from }} to {{ $to }} · sorted by suits stitched</p>
        </div>

        @if($workers->isEmpty())
            <p class="text-sm text-slate-400 px-5 py-8 text-center">{{ __('No data found.') }}</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-3 text-left w-8">#</th>
                        <th class="px-5 py-3 text-left">{{ __('Worker') }}</th>
                        <th class="px-5 py-3 text-center">{{ __('Suits') }}</th>
                        <th class="px-5 py-3 text-right">{{ __('Earned') }}</th>
                        <th class="px-5 py-3 text-right">{{ __('Paid') }}</th>
                        <th class="px-5 py-3 text-right">{{ __('Balance Due') }}</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($workers as $i => $worker)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 text-slate-400 text-xs font-medium">
                            @if($i === 0) 🥇
                            @elseif($i === 1) 🥈
                            @elseif($i === 2) 🥉
                            @else {{ $i + 1 }}
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <p class="font-medium text-slate-800">{{ $worker->name }}</p>
                            @if($worker->branch)
                            <p class="text-xs text-slate-400">{{ $worker->branch->name }}</p>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-center">
                            @if($totalSuits > 0)
                            <div class="flex items-center justify-center gap-2">
                                <span class="font-bold text-slate-800">{{ $worker->period_suits }}</span>
                                <div class="w-16 bg-slate-100 rounded-full h-1.5">
                                    <div class="bg-blue-500 h-1.5 rounded-full"
                                        style="width: {{ $totalSuits ? round($worker->period_suits / $workers->max('period_suits') * 100) : 0 }}%">
                                    </div>
                                </div>
                            </div>
                            @else
                            <span class="text-slate-400">0</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-blue-700">
                            Rs {{ number_format($worker->period_earned) }}
                        </td>
                        <td class="px-5 py-3 text-right text-green-700">
                            Rs {{ number_format($worker->total_paid) }}
                        </td>
                        <td class="px-5 py-3 text-right font-semibold {{ $worker->balance_due > 0 ? 'text-red-600' : 'text-slate-400' }}">
                            Rs {{ number_format($worker->balance_due) }}
                        </td>
                        <td class="px-5 py-3">
                            <a href="{{ route('workers.report', [$worker, 'from' => $from, 'to' => $to]) }}"
                                class="text-xs text-blue-600 hover:underline">{{ __('View') }} →</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

</div>
@endsection
