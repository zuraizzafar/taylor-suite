@extends('layouts.app')
@section('title', 'Edit Payment')
@section('page-title', 'Edit Payment')
@section('content')
<div class="pt-2 max-w-lg">

    <div class="mb-4">
        <a href="{{ route('orders.show', $payment->order) }}" class="text-sm text-blue-600 hover:underline">
            ← Back to {{ $payment->order->order_number }}
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <div class="mb-5 pb-4 border-b border-slate-100">
            <p class="text-xs text-slate-500 mb-0.5">Order</p>
            <p class="font-semibold text-slate-800">{{ $payment->order->order_number }} — {{ $payment->order->customer->name }}</p>
            <p class="text-xs text-slate-400 mt-1">
                Order total: <strong>Rs {{ number_format($payment->order->total_amount) }}</strong>
                &nbsp;·&nbsp; Balance after this edit:
                <strong class="text-slate-600">Rs {{ number_format($payment->order->balance_amount + $payment->amount) }} available</strong>
            </p>
        </div>

        <form method="POST" action="{{ route('payments.update', $payment) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div class="grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Amount (Rs) *</label>
                        <input type="number" name="amount" min="1" step="0.01" required
                            value="{{ old('amount', $payment->amount) }}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @error('amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Method *</label>
                        <select name="method"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @foreach(\App\Models\Payment::METHODS as $key => $label)
                            <option value="{{ $key }}" {{ old('method', $payment->method) === $key ? 'selected' : '' }}>{{ $label }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Date *</label>
                        <input type="date" name="payment_date" required
                            value="{{ old('payment_date', $payment->payment_date->toDateString()) }}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Reference</label>
                        <input type="text" name="reference" placeholder="Txn ID, cheque no…"
                            value="{{ old('reference', $payment->reference) }}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Note</label>
                    <input type="text" name="note" placeholder="Optional note"
                        value="{{ old('note', $payment->note) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                    Save Changes
                </button>
                <a href="{{ route('orders.show', $payment->order) }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-5 py-2 rounded-lg">
                    Cancel
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
