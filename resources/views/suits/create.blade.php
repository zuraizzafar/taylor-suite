@extends('layouts.app')
@section('title', __('New Suit'))
@section('page-title', __('New Suit'))
@section('content')
<div class="max-w-xl pt-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">

        {{-- Step 1: Customer selector (GET reload) --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Customer') }} *</label>
            <form method="GET" action="{{ route('suits.create') }}" id="customer-select-form">
                @if(request('order_id'))
                <input type="hidden" name="order_id" value="{{ request('order_id') }}">
                @endif
                <select name="customer_id"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    onchange="document.getElementById('customer-select-form').submit()">
                    <option value="">— Select Customer —</option>
                    @foreach($customers as $c)
                    <option value="{{ $c->id }}" {{ $selectedCustomer?->id == $c->id ? 'selected' : '' }}>
                        {{ $c->file_number }} – {{ $c->name }}
                    </option>
                    @endforeach
                </select>
            </form>
        </div>

        @if($selectedCustomer)

        {{-- Step 2: Suit details POST form --}}
        <form method="POST" action="{{ route('suits.store') }}">
            @csrf
            <input type="hidden" name="customer_id" value="{{ $selectedCustomer->id }}">

            @if($selectedOrder)
            <input type="hidden" name="order_id" value="{{ $selectedOrder->id }}">
            <div class="mb-4 p-2 bg-blue-50 rounded-lg text-sm text-blue-700">
                📋 Order: <strong>{{ $selectedOrder->order_number }}</strong>
            </div>
            @endif

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Use Measurement Set') }}</label>
                <select name="measurement_id"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— none —</option>
                    @foreach($selectedCustomer->measurements as $m)
                    <option value="{{ $m->id }}" {{ old('measurement_id') == $m->id ? 'selected' : '' }}>
                        {{ $m->label }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Suit Type') }} *</label>
                <input type="text" name="suit_type" value="{{ old('suit_type') }}"
                    list="suit-type-list"
                    placeholder="e.g. Kameez Shalwar, Sherwani…"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                <datalist id="suit-type-list">
                    @foreach($suitTypes as $st)
                    <option value="{{ $st->name }}">
                    @endforeach
                </datalist>
                @error('suit_type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Stitch Type') }}</label>
                <select name="stitch_type_id"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select stitch type —</option>
                    @foreach($stitchTypes as $st)
                    <option value="{{ $st->id }}" {{ old('stitch_type_id') == $st->id ? 'selected' : '' }}>
                        {{ $st->name }} — Rs {{ number_format($st->base_price) }} base
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">Worker override price will be used if set.</p>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Fabric Source') }}</label>
                <select name="fabric_id"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— {{ __("Customer's own cloth") }} —</option>
                    @foreach($fabrics as $f)
                    <option value="{{ $f->id }}" {{ old('fabric_id') == $f->id ? 'selected' : '' }}>
                        {{ $f->roll_number }} — {{ $f->fabric_type }} {{ $f->color }} ({{ number_format($f->available_meter, 1) }}m {{ __('available') }})
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">{{ __('Select a shop roll to auto-deduct stock, or leave as customer\'s own cloth.') }}</p>
                @error('fabric_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Fabric Size (meter)') }} *</label>
                    <input type="number" name="fabric_meter" value="{{ old('fabric_meter') }}"
                        step="0.1" min="0.1" placeholder="4.5"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @error('fabric_meter')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Fabric Description') }}</label>
                    <input type="text" name="fabric_description" value="{{ old('fabric_description') }}"
                        placeholder="Color, type…"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Assign Worker') }}</label>
                <select name="worker_id"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— none —</option>
                    @foreach($workers as $w)
                    <option value="{{ $w->id }}" {{ old('worker_id') == $w->id ? 'selected' : '' }}>
                        {{ $w->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6 notes-container">
                <div class="flex items-center justify-between mb-1">
                    <label class="block text-sm font-medium text-slate-700">{{ __('Notes') }}</label>
                    @php
                        $locale = app()->getLocale();
                        $notesStr = \App\Models\Setting::get("predefined_notes_{$locale}", '');
                        $notesList = array_filter(array_map('trim', explode("\n", $notesStr)));
                    @endphp
                    @if(!empty($notesList))
                    <select onchange="selectPredefinedNote(this)" class="text-xs border border-slate-300 rounded px-2 py-0.5 bg-slate-50 text-slate-600 focus:outline-none cursor-pointer">
                        <option value="">— Preset Notes —</option>
                        @foreach($notesList as $note)
                        <option value="{{ $note }}">{{ $note }}</option>
                        @endforeach
                        <option value="custom">+ Custom / Clear</option>
                    </select>
                    @endif
                </div>
                <textarea name="notes" rows="2"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">{{ __('Create Suit') }}</button>
                <a href="{{ route('suits.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-5 py-2 rounded-lg text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>

        @else
        <p class="text-sm text-slate-400">{{ __('Please select a customer to continue.') }}</p>
        @endif

    </div>
</div>
@endsection

