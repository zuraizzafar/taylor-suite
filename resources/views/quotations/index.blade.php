@extends('layouts.app')
@section('title', __('Quotations'))
@section('page-title', __('Quotations'))

@section('content')
<div class="pt-2">
    <div class="flex items-center justify-between mb-5">
        <form method="GET" action="{{ route('quotations.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ $search }}"
                placeholder="{{ __('Search') }}…"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <button class="bg-slate-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-slate-800">{{ __('Search') }}</button>
        </form>
        <a href="{{ route('quotations.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + {{ __('New Quotation') }}
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Quotation Number') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Customer') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Quotation Date') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Validity') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Total') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($quotations as $quotation)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-mono text-blue-700 font-semibold">{{ $quotation->quotation_number }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-800">{{ $quotation->customer->name }}</p>
                        <p class="text-xs text-slate-500">{{ $quotation->customer->file_number }}</p>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $quotation->quotation_date->format('d M Y') }}</td>
                    <td class="px-4 py-3 text-slate-600">
                        {{ $quotation->valid_until->format('d M Y') }}
                        @if($quotation->is_expired)
                        <span class="ml-1 text-[10px] bg-red-50 text-red-600 px-1.5 py-0.5 rounded-full">{{ __('Expired') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 font-medium">Rs {{ number_format($quotation->total_amount) }}</td>
                    <td class="px-4 py-3">
                        @if($quotation->status === 'converted')
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">{{ __('Converted') }}</span>
                        @else
                        <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">{{ __('Draft') }}</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-1 flex-wrap">
                            <a href="{{ route('quotations.show', $quotation) }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">{{ __('View') }}</a>
                            <a href="{{ route('quotations.pdf', $quotation) }}"
                               class="text-xs bg-green-50 hover:bg-green-100 text-green-700 px-2 py-1 rounded">PDF</a>
                            @if($quotation->status === 'draft')
                            <form method="POST" action="{{ route('quotations.destroy', $quotation) }}"
                                  onsubmit="return confirm('{{ __('Are you sure? This will permanently delete this quotation.') }}')">
                                @csrf
                                @method('DELETE')
                                <button type="submit"
                                        class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 rounded">{{ __('Delete') }}</button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">{{ __('No quotations found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($quotations->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">{{ $quotations->links() }}</div>
        @endif
    </div>
</div>
@endsection
