@extends('layouts.app')
@section('title', 'New Suit')
@section('page-title', 'New Suit')
@section('content')
<div class="max-w-xl pt-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">

        {{-- Step 1: Customer selector (GET reload) --}}
        <div class="mb-5">
            <label class="block text-sm font-medium text-slate-700 mb-1">Customer *</label>
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
                <label class="block text-sm font-medium text-slate-700 mb-1">Use Measurement Set</label>
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
                <label class="block text-sm font-medium text-slate-700 mb-1">Suit Type *</label>
                <input type="text" name="suit_type" value="{{ old('suit_type') }}"
                    placeholder="e.g. Shalwar Kameez, Pant Coat…"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('suit_type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Fabric Meter *</label>
                    <input type="number" name="fabric_meter" value="{{ old('fabric_meter') }}"
                        step="0.1" min="0.1" placeholder="4.5"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required>
                    @error('fabric_meter')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Fabric Description</label>
                    <input type="text" name="fabric_description" value="{{ old('fabric_description') }}"
                        placeholder="Color, type…"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">Assign Worker</label>
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

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
                <textarea name="notes" rows="2"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes') }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">Create Suit</button>
                <a href="{{ route('suits.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-5 py-2 rounded-lg text-sm">Cancel</a>
            </div>
        </form>

        @else
        <p class="text-sm text-slate-400">Please select a customer above — the form will load automatically.</p>
        @endif

    </div>
</div>
@endsection

