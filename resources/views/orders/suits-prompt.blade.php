@extends('layouts.app')
@section('title', __('Add Suit'))
@section('page-title', __('Order') . ': ' . $order->order_number)

@section('content')
<div class="max-w-xl pt-6">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-8 text-center space-y-4">
        <div class="text-4xl">👔</div>
        <h2 class="text-lg font-bold text-slate-800">{{ __('Add suits to this order?') }}</h2>
        <p class="text-sm text-slate-500">
            <span class="font-mono text-blue-700 font-semibold">{{ $order->order_number }}</span>
            · {{ $order->customer->name }}
        </p>
        <p class="text-sm text-slate-500">{{ __('Measurements are done. Add the suits for this order now, or skip and add them later from the order page.') }}</p>
        <div class="flex justify-center gap-3 pt-2">
            <a href="{{ route('suits.create', ['order_id' => $order->id, 'customer_id' => $order->customer_id]) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-6 py-2.5 rounded-lg text-sm">+ {{ __('Add Suit') }}</a>
            <a href="{{ route('orders.show', $order) }}"
               class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-6 py-2.5 rounded-lg text-sm">{{ __('Skip') }}</a>
        </div>
    </div>
</div>
@endsection
