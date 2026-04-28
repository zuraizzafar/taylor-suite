@extends('layouts.app')
@section('title', 'Edit Worker')
@section('page-title', 'Edit Worker')
@section('content')
<div class="max-w-2xl pt-4 space-y-5">

    {{-- Main worker form --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('workers.update', $worker) }}">
            @csrf @method('PUT')
            @include('workers._form')
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">Update Worker</button>
                <a href="{{ route('workers.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-5 py-2 rounded-lg text-sm">Cancel</a>
            </div>
        </form>
    </div>

    {{-- Per-stitch-type override rates --}}
    @if($stitchTypes->isNotEmpty())
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <h3 class="text-sm font-semibold text-slate-700 mb-1">Override Rates per Stitch Type</h3>
        <p class="text-xs text-slate-400 mb-4">Leave blank to use the stitch type's base price. Enter an amount to override for this worker only.</p>
        <form method="POST" action="{{ route('workers.update', $worker) }}">
            @csrf @method('PUT')
            {{-- Re-submit required worker fields so validation passes --}}
            <input type="hidden" name="name" value="{{ $worker->name }}">
            <input type="hidden" name="mobile" value="{{ $worker->mobile }}">
            <input type="hidden" name="rate_per_suit" value="{{ $worker->rate_per_suit }}">
            <input type="hidden" name="branch_id" value="{{ $worker->branch_id }}">
            <input type="hidden" name="user_id" value="{{ $worker->user_id }}">
            @if($worker->is_active)<input type="hidden" name="is_active" value="1">@endif

            <div class="space-y-3">
                @foreach($stitchTypes as $st)
                <div class="flex items-center justify-between gap-4">
                    <div>
                        <p class="text-sm font-medium text-slate-700">{{ $st->name }}</p>
                        <p class="text-xs text-slate-400">Base: Rs {{ number_format($st->base_price) }}</p>
                    </div>
                    <div class="flex items-center gap-2">
                        <span class="text-xs text-slate-500">Override (Rs)</span>
                        <input type="number" name="stitch_rates[{{ $st->id }}]"
                            value="{{ isset($rateMap[$st->id]) ? $rateMap[$st->id] : '' }}"
                            min="0" step="0.01" placeholder="Base price"
                            class="w-28 border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>
                @endforeach
            </div>
            <button type="submit" class="mt-4 bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-5 py-2 rounded-lg">
                Save Override Rates
            </button>
        </form>
    </div>
    @endif

    <div class="flex justify-end">
        <a href="{{ route('workers.report', $worker) }}"
            class="text-sm text-blue-600 hover:underline">📊 View Worker Report →</a>
    </div>
</div>
@endsection
