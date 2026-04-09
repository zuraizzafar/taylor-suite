@extends('layouts.app')

@section('title', 'Edit Customer')
@section('page-title', 'Edit Customer')

@section('content')
<div class="max-w-xl pt-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <div class="mb-4">
            <span class="text-xs font-mono bg-blue-100 text-blue-700 px-2 py-0.5 rounded">{{ $customer->file_number }}</span>
        </div>
        <form method="POST" action="{{ route('customers.update', $customer) }}">
            @csrf
            @method('PUT')
            @include('customers._form')
            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">
                    Update Customer
                </button>
                <a href="{{ route('customers.show', $customer) }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-5 py-2 rounded-lg text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
