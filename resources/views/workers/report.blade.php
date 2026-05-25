@extends('layouts.app')
@section('title', 'Worker Report – ' . $worker->name)
@section('page-title', 'Worker Report – ' . $worker->name)

@section('content')
<div class="pt-2 max-w-5xl space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">❌ {{ session('error') }}</div>
    @endif

    {{-- ── Period Selector ──────────────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <form method="GET" action="{{ route('workers.report', $worker) }}" class="flex flex-wrap items-end gap-3">
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">Quick Select</label>
                <div class="flex gap-1">
                    @foreach(['today' => 'Today', 'week' => 'This Week', 'month' => 'This Month'] as $key => $label)
                    <a href="{{ route('workers.report', [$worker, 'preset' => $key]) }}"
                        class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ $preset === $key ? 'bg-blue-600 text-white border-blue-600' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                        {{ $label }}
                    </a>
                    @endforeach
                </div>
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">From</label>
                <input type="date" name="from" value="{{ $from }}"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs font-semibold text-slate-600 mb-1">To</label>
                <input type="date" name="to" value="{{ $to }}"
                    class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <button type="submit"
                class="bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-4 py-1.5 rounded-lg">
                Apply
            </button>
            <a href="{{ route('workers.edit', $worker) }}" class="ml-auto text-xs text-blue-600 hover:underline">✏️ Edit Worker / Override Rates</a>
        </form>
    </div>

    {{-- ── Summary Cards ─────────────────────────────────────────── --}}
    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4">
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 text-center">
            <p class="text-xs text-slate-500 mb-1">Suits Stitched</p>
            <p class="text-3xl font-bold text-slate-800">{{ $totalSuits }}</p>
            <p class="text-xs text-slate-400 mt-1">{{ $from }} → {{ $to }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 text-center">
            <p class="text-xs text-slate-500 mb-1">{{ __('Total Earned') }}</p>
            <p class="text-2xl font-bold text-blue-600">Rs {{ number_format($totalEarned) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 text-center">
            <p class="text-xs text-slate-500 mb-1">Total Paid</p>
            <p class="text-2xl font-bold text-green-600">Rs {{ number_format($totalPaid) }}</p>
        </div>
        <div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4 text-center">
            <p class="text-xs text-slate-500 mb-1">Balance Due</p>
            <p class="text-2xl font-bold {{ $balanceDue > 0 ? 'text-red-600' : 'text-green-600' }}">
                Rs {{ number_format($balanceDue) }}
            </p>
        </div>
    </div>

    {{-- ── Suits Stitched in Period ───────────────────────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-700">Suits Stitched in Period</h2>
            <span class="text-xs text-slate-400">{{ $totalSuits }} suit{{ $totalSuits != 1 ? 's' : '' }}</span>
        </div>
        @if($stitchedSuits->isEmpty())
            <p class="text-sm text-slate-400 px-5 py-8 text-center">No suits stitched in this period.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-3 text-left">Date</th>
                        <th class="px-5 py-3 text-left">Suit Code</th>
                        <th class="px-5 py-3 text-left">Customer</th>
                        <th class="px-5 py-3 text-left">Stitch Type</th>
                        <th class="px-5 py-3 text-right">Earning</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($stitchedSuits as $suit)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 text-slate-500 text-xs">{{ $suit->stitching_started_at->format('d M Y') }}</td>
                        <td class="px-5 py-3">
                            <a href="{{ route('suits.show', $suit) }}" class="font-medium text-blue-600 hover:underline">{{ $suit->suit_code }}</a>
                        </td>
                        <td class="px-5 py-3 text-slate-700">{{ $suit->customer->name }}</td>
                        <td class="px-5 py-3">
                            @if($suit->stitchType)
                                <span class="bg-indigo-50 text-indigo-700 text-xs font-medium px-2 py-0.5 rounded-full">{{ $suit->stitchType->name }}</span>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3 text-right font-semibold text-slate-800">
                            {{ $suit->worker_earning ? 'Rs ' . number_format($suit->worker_earning) : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
                <tfoot class="border-t border-slate-200 bg-slate-50">
                    <tr>
                        <td colspan="4" class="px-5 py-3 text-xs font-semibold text-slate-600 text-right">Total:</td>
                        <td class="px-5 py-3 text-right font-bold text-blue-700">Rs {{ number_format($totalEarned) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
        @endif
    </div>

    {{-- ── Pending Suits ─────────────────────────────────────────── --}}
    @if($pendingSuits->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-amber-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-amber-100 bg-amber-50 flex items-center gap-2">
            <span>⏳</span>
            <h2 class="text-sm font-semibold text-amber-800">Pending / Cutting ({{ $pendingSuits->count() }})</h2>
            <p class="text-xs text-amber-600 ml-2">Assigned but not yet in stitching</p>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-3 text-left">Suit Code</th>
                        <th class="px-5 py-3 text-left">Customer</th>
                        <th class="px-5 py-3 text-left">Stitch Type</th>
                        <th class="px-5 py-3 text-left">{{ __('Status') }}</th>
                        <th class="px-5 py-3 text-right">Expected Earning</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($pendingSuits as $suit)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3">
                            <a href="{{ route('suits.show', $suit) }}" class="font-medium text-blue-600 hover:underline">{{ $suit->suit_code }}</a>
                        </td>
                        <td class="px-5 py-3 text-slate-700">{{ $suit->customer->name }}</td>
                        <td class="px-5 py-3">
                            @if($suit->stitchType)
                                <span class="bg-indigo-50 text-indigo-700 text-xs font-medium px-2 py-0.5 rounded-full">{{ $suit->stitchType->name }}</span>
                            @else
                                <span class="text-slate-400 text-xs">—</span>
                            @endif
                        </td>
                        <td class="px-5 py-3">
                            <span class="capitalize text-xs font-medium px-2 py-0.5 rounded-full
                                {{ $suit->status === 'cutting' ? 'bg-yellow-100 text-yellow-700' : 'bg-slate-100 text-slate-600' }}">
                                {{ $suit->status }}
                            </span>
                        </td>
                        <td class="px-5 py-3 text-right text-slate-500">
                            {{ $suit->worker_earning ? 'Rs ' . number_format($suit->worker_earning) : '—' }}
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @endif

    {{-- ── Salary Payment History + Record Payment ──────────────── --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h2 class="text-sm font-semibold text-slate-700">Salary Payment History</h2>
            <button onclick="document.getElementById('salaryPayForm').classList.toggle('hidden')"
                class="text-xs bg-green-600 hover:bg-green-700 text-white font-medium px-3 py-1.5 rounded-lg">
                + Record Payment
            </button>
        </div>

        {{-- Add Salary Payment Form --}}
        <div id="salaryPayForm" class="hidden px-5 py-4 bg-slate-50 border-b border-slate-100">
            <form method="POST" action="{{ route('workers.salary-payments.store', $worker) }}">
                @csrf
                <div class="grid grid-cols-2 md:grid-cols-3 gap-4 mb-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Period From *</label>
                        <input type="date" name="period_from" value="{{ $from }}" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Period To *</label>
                        <input type="date" name="period_to" value="{{ $to }}" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Total Suits *</label>
                        <input type="number" name="total_suits" value="{{ $totalSuits }}" min="0" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Total Earned (Rs) *</label>
                        <input type="number" name="total_earned" value="{{ $totalEarned }}" min="0" step="0.01" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Amount Paid (Rs) *</label>
                        <input type="number" name="amount_paid" value="{{ $balanceDue }}" min="0" step="0.01" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Notes</label>
                        <input type="text" name="notes" placeholder="Optional…"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div class="flex gap-3">
                    <button type="submit"
                        class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                        Record Salary Payment
                    </button>
                    <button type="button" onclick="document.getElementById('salaryPayForm').classList.add('hidden')"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-5 py-2 rounded-lg">
                        {{ __('Cancel') }}
                    </button>
                </div>
            </form>
        </div>

        @if($salaryPayments->isEmpty())
            <p class="text-sm text-slate-400 px-5 py-8 text-center">No salary payments recorded yet.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-xs font-semibold text-slate-500 uppercase tracking-wide">
                    <tr>
                        <th class="px-5 py-3 text-left">Period</th>
                        <th class="px-5 py-3 text-right">{{ __('Suits') }}</th>
                        <th class="px-5 py-3 text-right">Earned</th>
                        <th class="px-5 py-3 text-right">Paid</th>
                        <th class="px-5 py-3 text-left">Paid By</th>
                        <th class="px-5 py-3 text-left">Notes</th>
                        <th class="px-5 py-3"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($salaryPayments as $sp)
                    <tr class="hover:bg-slate-50">
                        <td class="px-5 py-3 text-slate-600 text-xs">
                            {{ $sp->period_from->format('d M Y') }} – {{ $sp->period_to->format('d M Y') }}
                        </td>
                        <td class="px-5 py-3 text-right text-slate-700">{{ $sp->total_suits }}</td>
                        <td class="px-5 py-3 text-right text-blue-700 font-semibold">Rs {{ number_format($sp->total_earned) }}</td>
                        <td class="px-5 py-3 text-right text-green-700 font-semibold">Rs {{ number_format($sp->amount_paid) }}</td>
                        <td class="px-5 py-3 text-slate-500 text-xs">{{ $sp->paidBy?->name ?? '—' }}</td>
                        <td class="px-5 py-3 text-slate-400 text-xs">{{ $sp->notes ?? '—' }}</td>
                        <td class="px-5 py-3">
                            <form method="POST" action="{{ route('workers.salary-payments.destroy', $sp) }}"
                                onsubmit="return confirm('Remove this payment?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:underline">{{ __('Remove') }}</button>
                            </form>
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
