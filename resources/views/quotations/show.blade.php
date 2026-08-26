@extends('layouts.app')
@section('title', $quotation->quotation_number)
@section('page-title', __('Quotation') . ': ' . $quotation->quotation_number)

@section('content')
<div class="pt-2 space-y-6">

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-slate-500 mb-1">{{ __('Quotation For') }}</p>
                <p class="text-lg font-bold text-slate-800">{{ $quotation->customer->name }}</p>
                <p class="text-sm text-slate-500">{{ $quotation->customer->file_number }} · {{ $quotation->customer->mobile }}</p>
            </div>
            <div class="text-right">
                <span class="font-mono text-blue-700 font-bold text-lg">{{ $quotation->quotation_number }}</span>
                <div>
                    @if($quotation->status === 'converted')
                    <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full font-medium">{{ __('Converted') }}</span>
                    @else
                    <span class="text-xs bg-amber-100 text-amber-700 px-2 py-0.5 rounded-full font-medium">{{ __('Draft') }}</span>
                    @endif
                </div>
                <div class="flex gap-2 mt-2 flex-wrap justify-end">
                    @if($quotation->status === 'draft')
                    <a href="{{ route('quotations.edit', $quotation) }}"
                       class="text-xs bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded-lg">{{ __('Edit') }}</a>
                    @endif
                    <a href="{{ route('quotations.pdf', $quotation) }}"
                       class="text-xs bg-green-600 hover:bg-green-700 text-white px-3 py-1 rounded-lg">🖸 {{ __('Print PDF') }}</a>
                    @if($quotation->status === 'draft')
                    <form method="POST" action="{{ route('quotations.convert', $quotation) }}"
                          onsubmit="return confirm('{{ __('Convert this quotation into a real order? This cannot be undone.') }}')">
                        @csrf
                        <button type="submit"
                            class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded-lg">✓ {{ __('Convert to Order') }}</button>
                    </form>
                    <form method="POST" action="{{ route('quotations.destroy', $quotation) }}"
                          onsubmit="return confirm('{{ __('Are you sure? This will permanently delete this quotation.') }}')">
                        @csrf
                        @method('DELETE')
                        <button type="submit"
                                class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-3 py-1 rounded-lg">{{ __('Delete') }}</button>
                    </form>
                    @else
                    <a href="{{ route('orders.show', $quotation->converted_order_id) }}"
                       class="text-xs bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded-lg">{{ __('View Order') }}</a>
                    @endif
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-5 pt-5 border-t border-slate-100">
            <div>
                <p class="text-xs text-slate-500">{{ __('Quotation Date') }}</p>
                <p class="text-sm font-semibold text-slate-700">{{ $quotation->quotation_date->format('d M Y') }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">{{ __('Validity') }}</p>
                <p class="text-sm font-semibold text-slate-700">
                    {{ $quotation->validity_days }} {{ __('Days') }}
                    <span class="text-xs text-slate-400">({{ $quotation->valid_until->format('d M Y') }})</span>
                </p>
            </div>
            <div>
                <p class="text-xs text-slate-500">{{ __('Total Quotation Amount') }}</p>
                <p class="text-sm font-semibold text-slate-700">Rs {{ number_format($quotation->total_amount) }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">{{ __('Advance Required') }} / {{ __('Remaining Balance') }}</p>
                <p class="text-sm font-semibold text-green-600">Rs {{ number_format($quotation->advance_amount) }} /
                    <span class="text-amber-700">Rs {{ number_format($quotation->balance_amount) }}</span>
                </p>
            </div>
        </div>

        @if($quotation->notes)
        <p class="mt-3 text-sm text-slate-500">📝 {{ $quotation->notes }}</p>
        @endif

        @if($quotation->design_reference)
        <div class="mt-3 pt-3 border-t border-slate-100">
            <p class="text-xs font-semibold text-slate-500 uppercase mb-1">{{ __('Design & Embroidery Reference') }}</p>
            <p class="text-sm text-slate-600">{{ $quotation->design_reference }}</p>
        </div>
        @endif
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100">
        <div class="px-5 py-4 border-b border-slate-100 flex items-center justify-between">
            <h3 class="font-semibold text-slate-700">🧾 {{ __('Items') }} ({{ $quotation->items->count() }})</h3>
        </div>
        <div class="overflow-x-auto">
            <table class="w-full text-sm">
                <thead class="bg-slate-50 text-slate-600">
                    <tr>
                        <th class="px-4 py-2 text-left font-medium">#</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Description') }}</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Qty') }}</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Rate') }}</th>
                        <th class="px-4 py-2 text-left font-medium">{{ __('Amount') }}</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-slate-50">
                    @forelse($quotation->items as $i => $item)
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-slate-500">{{ $i + 1 }}</td>
                        <td class="px-4 py-2 text-slate-700">{{ $item->description }}</td>
                        <td class="px-4 py-2 text-slate-600">{{ (float) $item->qty }}</td>
                        <td class="px-4 py-2 text-slate-600">Rs {{ number_format($item->rate) }}</td>
                        <td class="px-4 py-2 font-medium text-slate-800">Rs {{ number_format($item->line_total) }}</td>
                    </tr>
                    @empty
                    <tr><td colspan="5" class="px-4 py-6 text-center text-slate-400">{{ __('No items found.') }}</td></tr>
                    @endforelse
                </tbody>
                <tfoot class="bg-slate-50">
                    <tr>
                        <td colspan="4" class="px-4 py-2 text-right font-semibold text-slate-700">{{ __('Total Quotation Amount') }}</td>
                        <td class="px-4 py-2 font-bold text-slate-900">Rs {{ number_format($quotation->total_amount) }}</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

</div>
@endsection
