@extends('layouts.app')

@section('title', 'New Customer')
@section('page-title', 'New Customer')

@section('content')
<div class="max-w-xl pt-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('customers.store') }}">
            @csrf
            @include('customers._form')
            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">
                    Create Customer
                </button>
                <a href="{{ route('customers.index') }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-5 py-2 rounded-lg text-sm">
                    Cancel
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
