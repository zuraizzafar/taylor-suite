@extends('layouts.app')

@section('title', 'Customers')
@section('page-title', 'Customers')

@section('content')
<div class="pt-2">
    <div class="flex items-center justify-between mb-5">
        <form method="GET" action="{{ route('customers.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ $search }}"
                placeholder="Search name, mobile, file no…"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-72">
            <button class="bg-slate-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-slate-800">Search</button>
        </form>
        <a href="{{ route('customers.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + New Customer
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">File No</th>
                    <th class="px-4 py-3 text-left font-medium">Name</th>
                    <th class="px-4 py-3 text-left font-medium">Mobile</th>
                    <th class="px-4 py-3 text-left font-medium">Suits</th>
                    <th class="px-4 py-3 text-left font-medium">Address</th>
                    <th class="px-4 py-3 text-left font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($customers as $customer)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-mono font-semibold text-blue-700">{{ $customer->file_number }}</td>
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $customer->name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $customer->mobile }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full font-medium">
                            {{ $customer->suits_count }}
                        </span>
                    </td>
                    <td class="px-4 py-3 text-slate-500">{{ $customer->address ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('customers.show', $customer) }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">View</a>
                            <a href="{{ route('customers.edit', $customer) }}"
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded">Edit</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No customers found.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($customers->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">
            {{ $customers->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
