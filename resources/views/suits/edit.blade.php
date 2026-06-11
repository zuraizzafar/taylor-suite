@extends('layouts.app')
@section('title', __('Edit Suit'))
@section('page-title', 'Edit Suit – ' . $suit->suit_code)
@section('content')
<div class="max-w-xl pt-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('suits.update', $suit) }}">
            @csrf @method('PUT')

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Suit Type') }} *</label>
                <input type="text" name="suit_type" value="{{ old('suit_type', $suit->suit_type) }}"
                    list="suit-type-list"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                <datalist id="suit-type-list">
                    @foreach($suitTypes as $st)
                    <option value="{{ $st->name }}">
                    @endforeach
                </datalist>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Stitch Type') }}</label>
                <select name="stitch_type_id"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select stitch type —</option>
                    @foreach($stitchTypes as $st)
                    <option value="{{ $st->id }}" {{ old('stitch_type_id', $suit->stitch_type_id) == $st->id ? 'selected' : '' }}>
                        {{ $st->name }} — Rs {{ number_format($st->base_price) }} base
                    </option>
                    @endforeach
                </select>
                <p class="text-xs text-slate-400 mt-1">Changing this recalculates the worker earning.</p>
            </div>

            <div class="grid grid-cols-2 gap-4 mb-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Fabric Meter') }}</label>
                    <input type="number" name="fabric_meter" value="{{ old('fabric_meter', $suit->fabric_meter) }}"
                        step="0.1" min="0.1"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Fabric Description') }}</label>
                    <input type="text" name="fabric_description" value="{{ old('fabric_description', $suit->fabric_description) }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Measurement Set') }}</label>
                <select name="measurement_id"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— none —</option>
                    @foreach($measurements as $m)
                    <option value="{{ $m->id }}" {{ old('measurement_id', $suit->measurement_id) == $m->id ? 'selected' : '' }}>
                        {{ $m->label }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-4">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Assign Worker') }}</label>
                <select name="worker_id"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— none —</option>
                    @foreach($workers as $w)
                    <option value="{{ $w->id }}" {{ old('worker_id', $suit->worker_id) == $w->id ? 'selected' : '' }}>
                        {{ $w->name }}
                    </option>
                    @endforeach
                </select>
            </div>

            <div class="mb-6">
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Notes') }}</label>
                <textarea name="notes" rows="2"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $suit->notes) }}</textarea>
            </div>

            <div class="flex gap-3">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">{{ __('Update') }}</button>
                <a href="{{ route('suits.show', $suit) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-5 py-2 rounded-lg text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
