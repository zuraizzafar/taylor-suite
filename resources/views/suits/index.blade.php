@extends('layouts.app')
@section('title', 'Suits')
@section('page-title', 'Suits')

@section('content')
<div class="pt-2">
    <div class="flex items-center justify-between mb-5 flex-wrap gap-2">
        <form method="GET" action="{{ route('suits.index') }}" class="flex gap-2 flex-wrap">
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search code, customer…"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-52">
            <select name="status"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Statuses</option>
                @foreach(['pending','cutting','stitching','ready','delivered'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button class="bg-slate-700 text-white text-sm px-4 py-2 rounded-lg">Filter</button>
        </form>
        <a href="{{ route('suits.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + New Suit
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Code</th>
                    <th class="px-4 py-3 text-left font-medium">Customer</th>
                    <th class="px-4 py-3 text-left font-medium">Type</th>
                    <th class="px-4 py-3 text-left font-medium">Fabric</th>
                    <th class="px-4 py-3 text-left font-medium">Worker</th>
                    <th class="px-4 py-3 text-left font-medium">Status</th>
                    <th class="px-4 py-3 text-left font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($suits as $suit)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-2 font-mono font-semibold text-blue-700">{{ $suit->suit_code }}</td>
                    <td class="px-4 py-2">
                        <a href="{{ route('customers.show', $suit->customer) }}"
                           class="font-medium text-slate-800 hover:text-blue-600">{{ $suit->customer->name }}</a>
                        <p class="text-xs text-slate-400">{{ $suit->customer->file_number }}</p>
                    </td>
                    <td class="px-4 py-2 text-slate-700">{{ $suit->suit_type }}</td>
                    <td class="px-4 py-2 text-slate-600">{{ $suit->fabric_meter }}m</td>
                    <td class="px-4 py-2 text-slate-600">{{ $suit->worker?->name ?? '—' }}</td>
                    <td class="px-4 py-2">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $suit->status_badge }}">
                            {{ ucfirst($suit->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-2">
                        <div class="flex gap-1">
                            <a href="{{ route('suits.show', $suit) }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">View</a>
                            <a href="{{ route('suits.tag', $suit) }}"
                               class="text-xs bg-yellow-50 hover:bg-yellow-100 text-yellow-700 px-2 py-1 rounded">Tag</a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No suits found.</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($suits->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">{{ $suits->links() }}</div>
        @endif
    </div>
</div>
@endsection
