@extends('layouts.app')
@section('title', $order->order_number)
@section('page-title', 'Order: ' . $order->order_number)

@section('content')
<div class="pt-2 space-y-6">

    {{-- Order header card --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-slate-500 mb-1">Customer</p>
                <p class="text-lg font-bold text-slate-800">{{ $order->customer->name }}</p>
                <p class="text-sm text-slate-500">{{ $order->customer->file_number }} · {{ $order->customer->mobile }}</p>
            </div>
            <div class="text-right">
                <span class="font-mono text-blue-700 font-bold text-lg">{{ $order->order_number }}</span>
                <div class="flex gap-2 mt-2">
                    <a href="{{ route('orders.edit', $order) }}"
                       class="text-xs bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded-lg">Edit</a>
                    <a href="{{ route('orders.invoice', $order) }}"
                       class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg">🖨 Print PDF</a>
                    <a href="{{ route('suits.create', ['order_id' => $order->id, 'customer_id' => $order->customer_id]) }}"
                       class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg">+ Add Suit</a>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-5 pt-5 border-t border-slate-100">
            <div>
                <p class="text-xs text-slate-500">Order Date</p>
                <p class="text-sm font-semibold text-slate-700">{{ $order->order_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Delivery Date</p>
                <p class="text-sm font-semibold text-slate-700">{{ $order->delivery_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Total Amount</p>
                <p class="text-sm font-semibold text-slate-700">Rs {{ number_format($order->total_amount) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Advance / Balance</p>
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
                        <th class="px-4 py-2 text-left font-medium">Code</th>
                        <th class="px-4 py-2 text-left font-medium">Type</th>
                        <th class="px-4 py-2 text-left font-medium">Fabric</th>
                        <th class="px-4 py-2 text-left font-medium">Worker</th>
                        <th class="px-4 py-2 text-left font-medium">Status</th>
                        <th class="px-4 py-2 text-left font-medium">Actions</th>
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
                               class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">View</a>
                        </td>
                    </tr>
                    @empty
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No suits in this order yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

</div>
@endsection
