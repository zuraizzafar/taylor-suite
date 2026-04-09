@extends('layouts.app')
@section('title', 'Search Results')
@section('page-title', 'Search: "' . $q . '"')

@section('content')
<div class="pt-2 space-y-6">
    @if(strlen($q) < 2)
    <p class="text-slate-500 text-sm">Enter at least 2 characters to search.</p>
    @else

    {{-- Customers --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700">👤 Customers ({{ $customers->count() }})</h3>
        </div>
        @if($customers->isEmpty())
        <p class="px-5 py-4 text-sm text-slate-400">No customers found.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">File No</th>
                        <th class="px-4 py-2 text-left font-medium">Name</th>
                        <th class="px-4 py-2 text-left font-medium">Mobile</th>
                        <th class="px-4 py-2 text-left font-medium">Suits</th>
                        <th class="px-4 py-2 text-left font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($customers as $c)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 font-mono text-blue-700 font-semibold">{{ $c->file_number }}</td>
                        <td class="px-4 py-2 font-medium text-slate-800">{{ $c->name }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $c->mobile }}</td>
                        <td class="px-4 py-2 text-center">
                            <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $c->suits_count }}</span>
                        </td>
                        <td class="px-4 py-2">
                            <a href="{{ route('customers.show', $c) }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">View</a>
                        </td>
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    {{-- Suits --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700">👔 Suits by Code ({{ $suits->count() }})</h3>
        </div>
        @if($suits->isEmpty())
        <p class="px-5 py-4 text-sm text-slate-400">No suits found.</p>
        @else
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">Code</th>
                        <th class="px-4 py-2 text-left font-medium">Customer</th>
                        <th class="px-4 py-2 text-left font-medium">Type</th>
                        <th class="px-4 py-2 text-left font-medium">Status</th>
                        <th class="px-4 py-2 text-left font-medium">Action</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @foreach($suits as $suit)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 font-mono font-semibold text-blue-700">{{ $suit->suit_code }}</td>
                        <td class="px-4 py-2 text-slate-700">{{ $suit->customer->name }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ $suit->suit_type }}</td>
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
                    @endforeach
                </tbody>
            </table>
        </div>
        @endif
    </div>

    @endif
</div>
@endsection
