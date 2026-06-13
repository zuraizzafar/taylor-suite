@extends('layouts.app')
@section('title', $order->order_number)
@section('page-title', __('Order') . ': ' . $order->order_number)

@section('content')
<div class="pt-2 space-y-6">

    {{-- Order header card --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-slate-500 mb-1">{{ __('Customer') }}</p>
                <p class="text-lg font-bold text-slate-800">{{ $order->customer->name }}</p>
                <p class="text-sm text-slate-500">{{ $order->customer->file_number }} · {{ $order->customer->mobile }}</p>
            </div>
            <div class="text-right">
                <span class="font-mono text-blue-700 font-bold text-lg">{{ $order->order_number }}</span>
                <div class="flex gap-2 mt-2">
                    <a href="{{ route('orders.edit', $order) }}"
                       class="text-xs bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded-lg">{{ __('Edit') }}</a>
                    <a href="{{ route('orders.tags', $order) }}" target="_blank"
                       class="text-xs bg-purple-600 hover:bg-purple-700 text-white px-3 py-1 rounded-lg">🏷️ {{ __('Print All Tags') }}</a>
                    <a href="{{ route('orders.invoice', $order) }}"
                       class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg">🖸 {{ __('Print PDF') }}</a>
                    <a href="{{ route('suits.create', ['order_id' => $order->id, 'customer_id' => $order->customer_id]) }}"
                       class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg">+ {{ __('Add Suit') }}</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-5 pt-5 border-t border-slate-100">
            <div>
                <p class="text-xs text-slate-500">{{ __('Order Date') }}</p>
                <p class="text-sm font-semibold text-slate-700">{{ $order->order_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">{{ __('Delivery Date') }}</p>
                <p class="text-sm font-semibold text-slate-700">{{ $order->delivery_date?->format('d M Y') ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">{{ __('Total Amount') }}</p>
                <p class="text-sm font-semibold text-slate-700">Rs {{ number_format($order->total_amount) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">{{ __('Advance') }} / {{ __('Balance') }}</p>
                <p class="text-sm font-semibold text-green-600">Rs {{ number_format($order->advance_amount) }} /
                    <span class="{{ $order->balance_amount > 0 ? 'text-red-600' : 'text-green-600' }}">
                        Rs {{ number_format($order->balance_amount) }}
                    </span>
                </p>
            </div>
        </div>

        @if($order->notes)
        <p class="mt-3 text-sm text-slate-500">📝 {{ $order->notes }}</p>
        @endif

        @if(!empty($order->extras))
        <div class="mt-3 pt-3 border-t border-slate-100">
            <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Extras / Add-ons</p>
            <div class="flex flex-wrap gap-2">
                @foreach($order->extras as $extra)
                <span class="bg-amber-50 text-amber-800 border border-amber-200 text-xs px-2 py-1 rounded-full">
                    {{ $extra['name'] }} — Rs {{ number_format($extra['price']) }}
                </span>
                @endforeach
            </div>
        </div>
        @endif
    </div>

    {{-- Suits in this order --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-700">👔 Suits ({{ $order->suits->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Suit Code') }}</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Suit Type') }}</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Fabric') }}</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Worker') }}</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Status') }}</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Actions') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($order->suits as $suit)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 font-mono font-semibold text-blue-700">{{ $suit->suit_code }}</td>
                        <td class="px-4 py-2 text-slate-700">{{ $suit->suit_type }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $suit->fabric_meter }}m {{ $suit->fabric_description }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $suit->worker?->name ?? '—' }}</td>
                        <td class="px-4 py-2">
                            <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $suit->status_badge }}">
                                {{ ucfirst($suit->status) }}
                            </span>
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('suits.show', $suit) }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">{{ __('View') }}</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">{{ __('No suits found.') }}</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Payments --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <div>
                <h3 class="font-semibold text-slate-700">💳 {{ __('Payments') }}</h3>
                <p class="text-xs text-slate-400 mt-0.5">
                    {{ __('Collected') }}: <span class="text-green-600 font-semibold">Rs {{ number_format($order->advance_amount) }}</span>
                    &nbsp;·&nbsp; {{ __('Remaining') }}: <span class="{{ $order->balance_amount > 0 ? 'text-red-600' : 'text-green-600' }} font-semibold">Rs {{ number_format($order->balance_amount) }}</span>
                </p>
            </div>
        </div>

        {{-- Add payment form --}}
        <div class="px-5 py-4 border-b border-slate-100 bg-slate-50">
            <form method="POST" action="{{ route('orders.payments.store', $order) }}" class="grid grid-cols-2 md:grid-cols-5 gap-3 items-end">
                @csrf
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('Amount') }} (Rs) *</label>
                    <input type="number" name="amount" min="1" step="0.01" placeholder="e.g. 5000"
                        value="{{ old('amount') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('Payment Method') }} *</label>
                    <select name="method"
                        class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="cash">{{ __('Cash') }}</option>
                        <option value="bank_transfer">{{ __('Bank Transfer') }}</option>
                        <option value="cheque">{{ __('Cheque') }}</option>
                        <option value="online">{{ __('Online') }}</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('Date') }} *</label>
                    <input type="date" name="payment_date" value="{{ old('payment_date', today()->toDateString()) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                </div>
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">{{ __('Reference') }}</label>
                    <input type="text" name="reference" placeholder="Txn ID, cheque no…"
                        value="{{ old('reference') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <button type="submit"
                        class="w-full bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-1.5 rounded-lg">
                        + {{ __('Record Payment') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- Payment history --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Date') }}</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Amount') }}</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Payment Method') }}</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Reference') }}</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Received By') }}</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($order->payments->sortByDesc('payment_date') as $payment)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-slate-600">{{ $payment->payment_date->format('d M Y') }}</td>
                        <td class="px-4 py-2 font-semibold text-green-700">Rs {{ number_format($payment->amount) }}</td>
                        <td class="px-4 py-2">
                            <span class="text-xs bg-blue-50 text-blue-700 px-2 py-0.5 rounded-full">
                                {{ \App\Models\Payment::METHODS[$payment->method] ?? $payment->method }}
                            </span>
                        </td>
                        <td class="px-4 py-2 text-slate-500 text-xs">{{ $payment->reference ?? '—' }}</td>
                        <td class="px-4 py-2 text-slate-500 text-xs">{{ $payment->receivedBy?->name ?? '—' }}</td>
                        <td class="px-4 py-2 text-right">
                            <div class="flex justify-end gap-1">
                                <a href="{{ route('payments.edit', $payment) }}"
                                   class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded">{{ __('Edit') }}</a>
                                <form method="POST" action="{{ route('payments.destroy', $payment) }}"
                                    onsubmit="return confirm('{{ __('Are you sure?') }}')">
                                    @csrf @method('DELETE')
                                    <button class="text-xs text-red-500 hover:text-red-700 px-2 py-1">{{ __('Remove') }}</button>
                                </form>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-5 text-center text-slate-400 text-sm">{{ __('No payments recorded yet.') }}</td></tr>
                    @endforelse
                </tbody>
                @if($order->payments->isNotEmpty())
                <tfoot class="bg-slate-50">
                    <tr>
                        <td class="px-4 py-2 font-semibold text-slate-700" colspan="1">{{ __('Total Paid') }}</td>
                        <td class="px-4 py-2 font-bold text-green-700">Rs {{ number_format($order->payments->sum('amount')) }}</td>
                        <td colspan="4"></td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>
@endsection
