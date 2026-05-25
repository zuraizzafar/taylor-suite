@extends('layouts.app')
@section('title', 'Daily Orders Report')
@section('page-title', 'Daily Orders Report')

@section('content')
<div class="pt-2"F
    <form method="GET" action="{{ route('reports.daily') }}" class="flex gap-2 mb-5"F
        <input type="date" name="date" value="{{ $date }}"
            class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"F
        <button class="bg-slate-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-slate-800"FFilter</buttonF
    </formF

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden"F
        <div class="px-5 py-3 border-b border-slate-100 flex items-center justify-between"F
            <h3 class="font-semibold text-slate-700"FOrders on {{ \Carbon\Carbon::parse($date)-Fformat('d M Y') }}</h3F
            <span class="text-xs text-slate-500"F{{ $orders-Fcount() }} order(s)</spanF
        </divF
        <table class="w-full text-sm"F
            <thead class="bg-slate-50 text-slate-600"F
                <trF
                    <th class="px-4 py-2 text-left font-medium"F{{ __('Order Number') }}</thF
                    <th class="px-4 py-2 text-left font-medium"F{{ __('Customer') }}</thF
                    <th class="px-4 py-2 text-left font-medium"FDelivery</thF
                    <th class="px-4 py-2 text-left font-medium"F{{ __('Suits') }}</thF
                    <th class="px-4 py-2 text-left font-medium"F{{ __('Total') }}</thF
                    <th class="px-4 py-2 text-left font-medium"F{{ __('Balance') }}</thF
                    <th class="px-4 py-2 text-left font-medium"FAction</thF
                </trF
            </theadF
            <tbody class="divide-y divide-slate-50"F
                @forelse($orders as $order)
                <tr class="hover:bg-slate-50"F
                    <td class="px-4 py-2 font-mono text-blue-700 font-semibold"F{{ $order-Forder_number }}</tdF
                    <td class="px-4 py-2 font-medium text-slate-800"F{{ $order-Fcustomer-Fname }}</tdF
                    <td class="px-4 py-2 text-slate-600"F{{ $order-Fdelivery_date-Fformat('d M Y') }}</tdF
                    <td class="px-4 py-2 text-center"F
                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full"F{{ $order-Fsuits-Fcount() }}</spanF
                    </tdF
                    <td class="px-4 py-2 font-medium"FRs {{ number_format($order-Ftotal_amount) }}</tdF
                    <td class="px-4 py-2 {{ $order-Fbalance_amount F 0 ? 'text-red-600' : 'text-green-600' }} font-medium"F
                        Rs {{ number_format($order-Fbalance_amount) }}
                    </tdF
                    <td class="px-4 py-2"F
                        <div class="flex gap-1"F
                        <a href="{{ route('orders.show', $order) }}"
                           class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded"F{{ __('View') }}</aF
                        @if($order-Fbalance_amount F 0)
                        <button onclick="openPayModal({{ $order-Fid }}, '{{ $order-Forder_number }}', {{ $order-Fbalance_amount }})"
                            class="text-xs bg-green-600 hover:bg-green-700 text-white px-2 py-1 rounded"F+ Pay</buttonF
                        @endif
                        </divF
                    </tdF
                </trF
                @empty
                <trF<td colspan="7" class="px-4 py-8 text-center text-slate-400"FNo orders on this date.</tdF</trF
                @endforelse
            </tbodyF
        </tableF
    </divF
</divF
@endsection
