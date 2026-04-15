@extends('layouts.app')
@section('title', 'Add Expense')
@section('page-title', 'Add Expense')
@section('content')
<div class="max-w-xl pt-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <form method="POST" action="{{ route('expenses.store') }}">
            @csrf
            @include('expenses._form')
            <div class="flex gap-3 mt-6">
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">Save Expense</button>
                <a href="{{ route('expenses.index') }}" class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-5 py-2 rounded-lg text-sm">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection
