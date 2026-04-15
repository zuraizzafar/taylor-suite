@extends('layouts.app')
@section('title', 'Payments Report')
@section('page-title', 'Payments Report')
@section('content')
<div class="pt-2 space-y-5">

    {{-- Filters --}}
    <form method="GET" action="{{ route('reports.payments') }}"
          class="bg-white rounded-xl shadow-sm border border-slate-100 px-5 py-4 flex flex-wrap gap-3 items-end">
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
        <div>
            <label class="block text-xs font-medium text-slate-600 mb-1">Method</label>
            <select name="method" class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Methods</option>
                @foreach($methods as $key => $label)
                <option value="{{ $key }}" {{ $method === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
        </div>
        <div>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-1.5 rounded-lg">
                Filter
            </button>
        </div>
    </form>

    {{-- Summary cards --}}
    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
        <div class="md:col-span-2 bg-green-50 border border-green-100 rounded-xl p-4">
            <p class="text-xs text-green-600 font-medium mb-1">Total Collected</p>
            <p class="text-2xl font-bold text-green-800">Rs {{ number_format($totalAmount) }}</p>
            <p class="text-xs text-green-500 mt-1">{{ $payments->count() }} payment(s) · {{ $from }} to {{ $to }}</p>
        </div>
        @foreach($byMethod as $key => $sum)
        <div class="bg-white border border-slate-100 rounded-xl p-4 shadow-sm">
            <p class="text-xs text-slate-500 font-medium mb-1">{{ $methods[$key] ?? $key }}</p>
            <p class="text-xl font-bold text-slate-800">Rs {{ number_format($sum) }}</p>
        </div>
        @endforeach
    </div>

    {{-- Payments table --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Date</th>
                    <th class="px-4 py-3 text-left font-medium">Order</th>
                    <th class="px-4 py-3 text-left font-medium">Customer</th>
                    <th class="px-4 py-3 text-left font-medium">Amount</th>
                    <th class="px-4 py-3 text-left font-medium">Method</th>
                    <th class="px-4 py-3 text-left font-medium">Reference</th>
                    <th class="px-4 py-3 text-left font-medium">Received By</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($payments as $payment)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2 text-slate-600">{{ $payment->payment_date->format('d M Y') }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('orders.show', $payment->order) }}"
                           class="font-mono text-blue-700 hover:underline text-xs">
                            {{ $payment->order->order_number }}
                        </a>
                    </td>
                    <td class="px-4 py-2 text-slate-700">{{ $payment->order->customer->name }}</td>
                    <td class="px-4 py-2 font-semibold text-green-700">Rs {{ number_format($payment->amount) }}</td>
                    <td class="px-4 py-2">
                        <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
                            {{ \App\Models\Payment::METHODS[$payment->method] ?? $payment->method }}
                        </span>
                    </td>
                    <td class="px-4 py-2 text-slate-500 text-xs">{{ $payment->reference ?? '—' }}</td>
                    <td class="px-4 py-2 text-slate-500 text-xs">{{ $payment->receivedBy?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No payments in this period.</td></tr>
                @endforelse
            </tbody>
            @if($payments->isNotEmpty())
            <tfoot class="bg-slate-50">
                <tr>
                    <td colspan="3" class="px-4 py-2 font-semibold text-slate-700 text-right">Total:</td>
                    <td class="px-4 py-2 font-bold text-green-700">Rs {{ number_format($totalAmount) }}</td>
                    <td colspan="3"></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

</div>
@endsection
