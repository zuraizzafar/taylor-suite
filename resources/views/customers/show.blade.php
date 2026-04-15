@extends('layouts.app')

@section('title', $customer->name . ' – ' . $customer->file_number)
@section('page-title', $customer->name)

@section('content')
<div class="pt-2 space-y-6">

    {{-- Customer Info Card --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-start justify-between">
            <div class="flex items-center gap-4">
                <div class="w-12 h-12 rounded-full bg-blue-100 flex items-center justify-center text-xl font-bold text-blue-700">
                    {{ strtoupper(substr($customer->name, 0, 1)) }}
                </div>
                <div>
                    <h2 class="text-lg font-bold text-slate-800">{{ $customer->name }}</h2>
                    <p class="text-sm text-slate-500">{{ $customer->mobile }}</p>
                    @if($customer->address)
                    <p class="text-xs text-slate-400 mt-0.5">{{ $customer->address }}</p>
                    @endif
                </div>
            </div>
            <div class="text-right">
                <span class="font-mono text-blue-700 font-bold text-lg">{{ $customer->file_number }}</span>
                <div class="flex gap-2 mt-2">
                    <a href="{{ route('customers.edit', $customer) }}"
                       class="text-xs bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded-lg">Edit</a>
                    <a href="{{ route('orders.create', ['customer_id' => $customer->id]) }}"
                       class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg">+ New Order</a>
                    <a href="{{ route('measurements.create', $customer) }}"
                       class="text-xs bg-slate-700 hover:bg-slate-800 text-white px-3 py-1 rounded-lg">+ Measurement</a>
                </div>
            </div>
        </div>
    </div>

    {{-- Measurements --}}
    @if($customer->measurements->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-700">📏 Measurements</h3>
        </div>
        <div class="divide-y divide-slate-50">
            @foreach($customer->measurements as $m)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-800">{{ $m->label }}</p>
                    <p class="text-xs text-slate-500 mt-0.5">
                        Qameez: L{{ $m->q_length }} S{{ $m->q_shoulder }} C{{ $m->q_chest }} ·
                        Shalwar: L{{ $m->s_length }} W{{ $m->s_waist }}
                    </p>
                </div>
                <a href="{{ route('measurements.edit', [$customer, $m]) }}"
                   class="text-xs bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded-lg">Edit</a>
            </div>
            @endforeach
        </div>
    </div>
    @endif

    {{-- Suits --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-700">👔 Suits ({{ $customer->suits->count() }})</h3>
            <a href="{{ route('suits.create', ['customer_id' => $customer->id]) }}"
               class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg">+ Add Suit</a>
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
                    @forelse($customer->suits as $suit)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 font-mono font-semibold text-blue-700">{{ $suit->suit_code }}</td>
                        <td class="px-4 py-2 text-slate-700">{{ $suit->suit_type }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $suit->fabric_meter }}m</td>
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
                    <tr><td colspan="6" class="px-4 py-6 text-center text-slate-400">No suits yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Orders --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700">🧾 Orders ({{ $customer->orders->count() }})</h3>
        </div>
        <div class="divide-y divide-slate-50">
            @forelse($customer->orders as $order)
            <div class="px-5 py-3 flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-slate-800">{{ $order->order_number }}</p>
                    <p class="text-xs text-slate-500">
                        {{ $order->order_date->format('d M Y') }} · Delivery: {{ $order->delivery_date->format('d M Y') }}
                        · {{ $order->suits->count() }} suit(s)
                    </p>
                </div>
                <div class="flex items-center gap-3">
                    <div class="text-right">
                        <p class="text-sm font-semibold">Rs {{ number_format($order->total_amount) }}</p>
                        <p class="text-xs text-red-500">Bal: Rs {{ number_format($order->balance_amount) }}</p>
                    </div>
                    <a href="{{ route('orders.show', $order) }}"
                       class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">View</a>
                    @if($order->balance_amount > 0)
                    <button onclick="openPayModal({{ $order->id }}, '{{ $order->order_number }}', {{ $order->balance_amount }})"
                        class="text-xs bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded">+ Pay</button>
                    @endif
                </div>
            </div>
            @empty
            <p class="px-5 py-4 text-sm text-slate-400">No orders yet.</p>
            @endforelse
        </div>
    </div>

</div>
@endsection
