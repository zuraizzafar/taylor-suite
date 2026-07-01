@extends('layouts.app')
@section('title', __('Fabric Sale'))
@section('page-title', __('Fabric Sale'))

@section('content')
<div class="max-w-xl pt-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6" x-data="fabricSale({{ $fabric ? $fabric->toJson() : 'null' }})">
        @if(session('error'))
            <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">❌ {{ session('error') }}</div>
        @endif
        @error('meter')<div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">❌ {{ $message }}</div>@enderror

        <form method="POST" action="{{ route('fabric-sales.store') }}">
            @csrf
            <input type="hidden" name="fabric_id" x-model="fabricId">

            <div class="mb-4">
                <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Scan QR / Enter Roll Number') }}</label>
                <div class="flex gap-2">
                    <input type="text" x-model="rollInput" @keydown.enter.prevent="lookup()"
                        placeholder="{{ __('Roll number') }}"
                        class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="button" @click="lookup()" class="bg-slate-700 hover:bg-slate-800 text-white text-sm px-4 py-2 rounded-lg">{{ __('Find') }}</button>
                </div>
                <p class="text-xs mt-1" x-show="fabricLabel" x-text="fabricLabel"></p>
                <p class="text-xs text-red-500 mt-1" x-show="notFound" x-cloak>{{ __('Fabric roll not found.') }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Customer Name') }} *</label>
                    <input type="text" name="customer_name" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Mobile Number') }}</label>
                    <input type="text" name="customer_mobile"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-4 mb-4">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Meter') }} *</label>
                    <input type="number" name="meter" step="0.01" min="0.01" :max="availableMeter" x-model.number="meter" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Rate (Rs)') }}</label>
                    <input type="text" :value="rate" disabled
                        class="w-full border border-slate-200 bg-slate-50 rounded-lg px-3 py-2 text-sm">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Total (Rs)') }}</label>
                    <input type="text" :value="total.toFixed(2)" disabled
                        class="w-full border border-slate-200 bg-slate-50 rounded-lg px-3 py-2 text-sm font-semibold">
                </div>
            </div>
            <p class="text-xs text-slate-400 mb-4" x-show="fabricId">{{ __('Available') }}: <span x-text="availableMeter"></span>m</p>

            <div class="flex gap-3">
                <button type="submit" :disabled="!fabricId"
                    class="bg-blue-600 hover:bg-blue-700 disabled:opacity-40 text-white font-medium px-5 py-2 rounded-lg text-sm">
                    {{ __('Sell & Print Invoice') }}
                </button>
                <a href="{{ route('fabrics.index') }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-5 py-2 rounded-lg text-sm">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
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

function fabricSale(initial) {
    return {
        fabricId: initial ? initial.id : null,
        rollInput: initial ? initial.roll_number : '',
        fabricLabel: initial ? (initial.fabric_type + ' — ' + initial.color + ' (' + initial.roll_number + ')') : '',
        availableMeter: initial ? parseFloat(initial.available_meter) : 0,
        rate: initial ? parseFloat(initial.sale_price) : 0,
        meter: 0,
        notFound: false,
        get total() {
            return (this.meter || 0) * (this.rate || 0);
        },
        lookup() {
            this.notFound = false;
            fetch(`{{ route('fabrics.lookup') }}?q=${encodeURIComponent(this.rollInput)}`)
                .then(r => r.json())
                .then(data => {
                    if (!data.found) { this.notFound = true; this.fabricId = null; return; }
                    this.fabricId = data.id;
                    this.availableMeter = data.available_meter;
                    this.rate = data.sale_price;
                    this.fabricLabel = data.fabric_type + ' — ' + data.color + ' (' + data.roll_number + ')';
                });
        }
    }
}
</script>
@endpush
@endsection
