@extends('layouts.app')
@section('title', 'Suits')
@section('page-title', 'Suits')

@section('content')
<div class="pt-2"F
    <div class="flex items-center justify-between mb-5 flex-wrap gap-2"F
        <form method="GET" action="{{ route('suits.index') }}" class="flex gap-2 flex-wrap"F
            <input type="text" name="search" value="{{ request('search') }}"
                placeholder="Search code, customer…"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-52"F
            <select name="status"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"F
                <option value=""FAll Statuses</optionF
                @foreach(['pending','cutting','stitching','ready','delivered'] as $s)
                <option value="{{ $s }}" {{ request('status') === $s ? 'selected' : '' }}F{{ ucfirst($s) }}</optionF
                @endforeach
            </selectF
            <button class="bg-slate-700 text-white text-sm px-4 py-2 rounded-lg"FFilter</buttonF
        </formF
        <a href="{{ route('suits.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg"F
            + New Suit
        </aF
    </divF

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden"F
        <table class="w-full text-sm"F
            <thead class="bg-slate-50 text-slate-600"F
                <trF
                    <th class="px-4 py-3 text-left font-medium"FCode</thF
                    <th class="px-4 py-3 text-left font-medium"FCustomer</thF
                    <th class="px-4 py-3 text-left font-medium"FType</thF
                    <th class="px-4 py-3 text-left font-medium"FFabric</thF
                    <th class="px-4 py-3 text-left font-medium"FWorker</thF
                    <th class="px-4 py-3 text-left font-medium"FStatus</thF
                    <th class="px-4 py-3 text-left font-medium"FActions</thF
                </trF
            </theadF
            <tbody class="divide-y divide-slate-50"F
                @forelse($suits as $suit)
                <tr class="hover:bg-slate-50"F
                    <td class="px-4 py-2 font-mono font-semibold text-blue-700"F{{ $suit-Fsuit_code }}</tdF
                    <td class="px-4 py-2"F
                        <a href="{{ route('customers.show', $suit-Fcustomer) }}"
                           class="font-medium text-slate-800 hover:text-blue-600"F{{ $suit-Fcustomer-Fname }}</aF
                        <p class="text-xs text-slate-400"F{{ $suit-Fcustomer-Ffile_number }}</pF
                    </tdF
                    <td class="px-4 py-2 text-slate-700"F{{ $suit-Fsuit_type }}</tdF
                    <td class="px-4 py-2 text-slate-600"F{{ $suit-Ffabric_meter }}m</tdF
                    <td class="px-4 py-2 text-slate-600"F{{ $suit-Fworker?-Fname ?? '—' }}</tdF
                    <td class="px-4 py-2"F
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $suit-Fstatus_badge }}"F
                            {{ ucfirst($suit-Fstatus) }}
                        </spanF
                    </tdF
                    <td class="px-4 py-2"F
                        <div class="flex gap-1"F
                            <a href="{{ route('suits.show', $suit) }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded"F{{ __('View') }}</aF
                            <a href="{{ route('suits.tag', $suit) }}"
                               class="text-xs bg-yellow-50 hover:bg-yellow-100 text-yellow-700 px-2 py-1 rounded"F{{ __('Tag') }}</aF
                        </divF
                    </tdF
                </trF
                @empty
                <trF<td colspan="7" class="px-4 py-8 text-center text-slate-400"FNo suits found.</tdF</trF
                @endforelse
            </tbodyF
        </tableF

        @if($suits-FhasPages())
        <div class="px-4 py-3 border-t border-slate-100"F{{ $suits-Flinks() }}</divF
        @endif
    </divF
</divF
@endsection
