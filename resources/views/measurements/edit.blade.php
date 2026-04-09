@extends('layouts.app')
@section('title', 'Edit Measurement')
@section('page-title', 'Edit Measurement – ' . $customer->name)
@section('content')
<div class="max-w-3xl pt-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('measurements.update', [$customer, $measurement]) }}">
            @csrf @method('PUT')
            @include('measurements._form')
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">Update Measurements</button>
                <a href="{{ route('customers.show', $customer) }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-5 py-2 rounded-lg text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
