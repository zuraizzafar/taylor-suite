@extends('layouts.app')
@section('title', __('Fabric Stock'))
@section('page-title', __('Fabric Stock'))

@section('content')
<div class="pt-2 space-y-6">

    @if(session('success'))
        <div class="bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">❌ {{ session('error') }}</div>
    @endif

    {{-- Dashboard metric cards --}}
    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-6 gap-4">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Total Fabric Rolls') }}</p>
            <p class="text-xl font-bold text-slate-800 mt-1">{{ $stats['total_rolls'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Total Stock (Meters)') }}</p>
            <p class="text-xl font-bold text-slate-800 mt-1">{{ number_format($stats['total_meters'], 1) }}m</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Low Stock Items') }}</p>
            <p class="text-xl font-bold {{ $stats['low_stock_items'] > 0 ? 'text-red-600' : 'text-slate-800' }} mt-1">{{ $stats['low_stock_items'] }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Today Sales') }}</p>
            <p class="text-xl font-bold text-slate-800 mt-1">Rs {{ number_format($stats['today_sales']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Total Stock Value') }}</p>
            <p class="text-xl font-bold text-slate-800 mt-1">Rs {{ number_format($stats['total_stock_value']) }}</p>
        </div>
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-4">
            <p class="text-xs text-slate-500">{{ __('Profit') }}</p>
            <p class="text-xl font-bold text-green-600 mt-1">Rs {{ number_format($stats['profit']) }}</p>
        </div>
    </div>

    <div class="flex items-center justify-between">
        <form method="GET" action="{{ route('fabrics.index') }}" class="flex gap-2">
            <input type="text" name="search" value="{{ $search }}"
                placeholder="{{ __('Search roll no, color, type') }}…"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 w-64">
            <button class="bg-slate-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-slate-800">{{ __('Search') }}</button>
        </form>
        <div class="flex gap-2">
            <a href="{{ route('fabric-sales.create') }}"
               class="bg-green-600 hover:bg-green-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                🧾 {{ __('Fabric Sale') }}
            </a>
            <a href="{{ route('fabrics.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                + {{ __('Add Fabric') }}
            </a>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">{{ __('QR') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Fabric') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Color') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Roll Number') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Available') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Sale Price') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Status') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($fabrics as $fabric)
                <tr class="hover:bg-slate-50" x-data="{ addOpen: false, reduceOpen: false }">
                    <td class="px-4 py-3">
                        <a href="{{ route('fabrics.sticker', $fabric) }}" target="_blank" class="text-xs text-blue-600 hover:underline">{{ __('Sticker') }}</a>
                    </td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-800">{{ $fabric->fabric_type }}</p>
                        <p class="text-xs text-slate-500">{{ $fabric->brand }} @if($fabric->design_code) · {{ $fabric->design_code }} @endif</p>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $fabric->color }}</td>
                    <td class="px-4 py-3 font-mono text-blue-700 font-semibold">{{ $fabric->roll_number }}</td>
                    <td class="px-4 py-3">{{ number_format($fabric->available_meter, 1) }}m</td>
                    <td class="px-4 py-3 font-medium">Rs {{ number_format($fabric->sale_price) }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $fabric->status_badge }}">
                            {{ ucfirst(str_replace('_', ' ', $fabric->status)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex flex-wrap gap-1">
                            <a href="{{ route('fabrics.edit', $fabric) }}"
                               class="text-xs bg-slate-100 hover:bg-slate-200 px-2 py-1 rounded">{{ __('Edit') }}</a>
                            <a href="{{ route('fabrics.history', $fabric) }}"
                               class="text-xs bg-purple-50 hover:bg-purple-100 text-purple-700 px-2 py-1 rounded">{{ __('History') }}</a>
                            <button @click="addOpen = true" type="button"
                                class="text-xs bg-green-50 hover:bg-green-100 text-green-700 px-2 py-1 rounded">+ {{ __('Meter') }}</button>
                            <button @click="reduceOpen = true" type="button"
                                class="text-xs bg-yellow-50 hover:bg-yellow-100 text-yellow-700 px-2 py-1 rounded">− {{ __('Meter') }}</button>
                            <form method="POST" action="{{ route('fabrics.destroy', $fabric) }}"
                                  onsubmit="return confirm('{{ __('Are you sure? This will archive the fabric roll.') }}')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 rounded">{{ __('Delete') }}</button>
                            </form>
                        </div>

                        {{-- Add Meter modal --}}
                        <div x-show="addOpen" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" style="display:none">
                            <div class="bg-white rounded-xl p-5 w-80" @click.outside="addOpen = false">
                                <h3 class="text-sm font-semibold mb-3">{{ __('Add Meter') }} — {{ $fabric->roll_number }}</h3>
                                <form method="POST" action="{{ route('fabrics.add-meter', $fabric) }}">
                                    @csrf @method('PATCH')
                                    <input type="number" name="meter" step="0.01" min="0.01" placeholder="{{ __('Meters') }}" required
                                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm mb-2">
                                    <input type="text" name="note" placeholder="{{ __('Note (optional)') }}"
                                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm mb-3">
                                    <div class="flex gap-2">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 rounded-lg">{{ __('Save') }}</button>
                                        <button type="button" @click="addOpen = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium px-4 py-2 rounded-lg">{{ __('Cancel') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>

                        {{-- Reduce Meter modal --}}
                        <div x-show="reduceOpen" x-cloak class="fixed inset-0 bg-black/40 flex items-center justify-center z-50" style="display:none">
                            <div class="bg-white rounded-xl p-5 w-80" @click.outside="reduceOpen = false">
                                <h3 class="text-sm font-semibold mb-3">{{ __('Reduce Meter') }} — {{ $fabric->roll_number }}</h3>
                                <form method="POST" action="{{ route('fabrics.reduce-meter', $fabric) }}">
                                    @csrf @method('PATCH')
                                    <input type="number" name="meter" step="0.01" min="0.01" max="{{ $fabric->available_meter }}" placeholder="{{ __('Meters') }}" required
                                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm mb-2">
                                    <select name="reason" required class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm mb-2">
                                        <option value="damage">{{ __('Damage') }}</option>
                                        <option value="adjustment">{{ __('Adjustment') }}</option>
                                    </select>
                                    <input type="text" name="note" placeholder="{{ __('Note (optional)') }}"
                                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm mb-3">
                                    <div class="flex gap-2">
                                        <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-2 rounded-lg">{{ __('Save') }}</button>
                                        <button type="button" @click="reduceOpen = false" class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium px-4 py-2 rounded-lg">{{ __('Cancel') }}</button>
                                    </div>
                                </form>
                            </div>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="8" class="px-4 py-8 text-center text-slate-400">{{ __('No fabric rolls found.') }}</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($fabrics->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">{{ $fabrics->links() }}</div>
        @endif
    </div>
</div>

@push('scripts')
<script>
if (!window.Alpine) {
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
    s.defer = true;
    document.head.appendChild(s);
}
</script>
@endpush
@endsection
