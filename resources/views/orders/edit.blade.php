@extends('layouts.app')
@section('title', 'Edit Order')
@section('page-title', 'Edit Order')
@section('content')
<div class="max-w-2xl pt-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <div class="mb-4">
            <span class="font-mono text-blue-700 font-semibold">{{ $order->order_number }}</span>
            <span class="text-slate-500 text-sm ml-2">– {{ $order->customer->name }}</span>
        </div>
        <form method="POST" action="{{ route('orders.update', $order) }}">
            @csrf @method('PUT')
            @include('orders._form')
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">Update Order</button>
                <a href="{{ route('orders.show', $order) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-5 py-2 rounded-lg text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
