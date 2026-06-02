@extends('layouts.app')
@section('title', 'Add Measurement')
@section('page-title', 'Add Measurement – ' . $customer->name)
@section('content')
@php($cancelUrl = request('redirect_to', route('customers.show', $customer)))
<div class="max-w-3xl pt-4">
    @if($lastMeasurement)
    <div class="mb-4 p-3 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-700">
        💡 Last measurement set: <strong>{{ $lastMeasurement->label }}</strong>. You can start a new set below.
    </div>
    @endif
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('measurements.store', $customer) }}">
            @csrf
            <input type="hidden" name="redirect_to" value="{{ request('redirect_to') }}">
            <input type="hidden" name="suit_id" value="{{ request('suit_id') }}">
            @include('measurements._form')
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">{{ __('Save Measurements') }}</button>
                <a href="{{ $cancelUrl }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-5 py-2 rounded-lg text-sm">{{ __('Cancel') }}</a>
            </div>
        </form>
    </div>
</div>
@endsection
