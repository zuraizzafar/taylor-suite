@extends('layouts.app')
@section('title', 'Pending Balances')
@section('page-title', 'Pending Customer Balances')

@section('content')
<div class="space-y-5">

    {{-- Grand Total Banner --}}
    <div class="bg-red-50 border border-red-200 rounded-xl p-4 flex items-center justify-between">
        <div>
            <p class="text-xs text-red-600 font-semibold uppercase tracking-wide">Total Outstanding Dues</p>
            <p class="text-2xl font-bold text-red-800">Rs {{ number_format($grandTotal) }}</p>
        </div>
        <div class="text-sm text-red-500">{{ $customers->count() }} customers with pending dues</div>
    </div>

    {{-- Table --}}
    <div class="bg-white border border-slate-200 rounded-xl overflow-hidden">
        <table class="w-full text-sm">
            <thead>
                <tr class="bg-slate-50 text-xs text-slate-500 border-b border-slate-200">
                    <th class="px-4 py-3 text-left font-medium">File No</th>
                    <th class="px-4 py-3 text-left font-medium">Customer</th>
                    <th class="px-4 py-3 text-left font-medium">Mobile</th>
                    <th class="px-4 py-3 text-right font-medium">Pending Orders</th>
                    <th class="px-4 py-3 text-right font-medium">Total Due</th>
                    <th class="px-4 py-3 text-center font-medium">Action</th>
                </tr>
            </thead>
            <tbody>
                @forelse($customers as $customer)
                <tr class="border-b border-slate-50 hover:bg-slate-50">
                    <td class="px-4 py-3 font-mono text-xs font-semibold text-slate-500">{{ $customer->file_number }}</td>
                    <td class="px-4 py-3 font-semibold text-slate-800">{{ $customer->name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $customer->mobile }}</td>
                    <td class="px-4 py-3 text-right">
                        <span class="bg-red-100 text-red-700 text-xs font-semibold px-2 py-0.5 rounded-full">
                            {{ $customer->orders->count() }} orders
                        </span>
                    </td>
                    <td class="px-4 py-3 text-right font-bold text-red-700">
                        Rs {{ number_format($customer->total_outstanding) }}
                    </td>
                    <td class="px-4 py-3 text-center">
                        <a href="{{ route('customers.show', $customer) }}"
                            class="text-xs text-blue-600 hover:underline">View</a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="text-center py-10 text-slate-400">No pending balances found.</td></tr>
                @endforelse
            </tbody>

            @if($customers->isNotEmpty())
            <tfoot>
                <tr class="bg-red-50">
                    <td colspan="4" class="px-4 py-3 text-right font-semibold text-red-800 text-xs uppercase">Grand Total Outstanding</td>
                    <td class="px-4 py-3 text-right font-bold text-red-800">Rs {{ number_format($grandTotal) }}</td>
                    <td></td>
                </tr>
            </tfoot>
            @endif
        </table>
    </div>

</div>
@endsection
