@extends('layouts.app')
@section('title', 'Expenses')
@section('page-title', 'Expenses')
@section('content')
<div class="pt-2">
    <div class="flex items-center justify-between mb-5 flex-wrap gap-2">
        <form method="GET" action="{{ route('expenses.index') }}" class="flex gap-2 flex-wrap">
            <select name="category"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">All Categories</option>
                @foreach(\App\Models\Expense::CATEGORIES as $key => $label)
                <option value="{{ $key }}" {{ request('category') === $key ? 'selected' : '' }}>{{ $label }}</option>
                @endforeach
            </select>
            <input type="date" name="from" value="{{ request('from') }}"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <input type="date" name="to" value="{{ request('to') }}"
                class="text-sm border border-slate-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            <button class="bg-slate-700 text-white text-sm px-4 py-2 rounded-lg hover:bg-slate-800">Filter</button>
        </form>
        <a href="{{ route('expenses.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + Add Expense
        </a>
    </div>

    {{-- Total banner --}}
    <div class="bg-red-50 border border-red-200 rounded-xl px-5 py-3 mb-4 flex items-center justify-between">
        <span class="text-sm text-red-700 font-medium">Total Expenses (filtered)</span>
        <span class="text-lg font-bold text-red-700">Rs {{ number_format($expenses->sum('amount')) }}</span>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Date</th>
                    <th class="px-4 py-3 text-left font-medium">Category</th>
                    <th class="px-4 py-3 text-left font-medium">Description</th>
                    <th class="px-4 py-3 text-left font-medium">Branch</th>
                    <th class="px-4 py-3 text-left font-medium">Amount</th>
                    <th class="px-4 py-3 text-left font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($expenses as $expense)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-700">{{ $expense->date->format('d M Y') }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs bg-slate-100 text-slate-700 px-2 py-0.5 rounded-full">{{ $expense->category }}</span>
                    </td>
                    <td class="px-4 py-3 text-slate-600">{{ $expense->description ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $expense->branch?->name ?? 'Main' }}</td>
                    <td class="px-4 py-3 font-semibold text-red-600">Rs {{ number_format($expense->amount) }}</td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('expenses.edit', $expense) }}"
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded">Edit</a>
                            <form method="POST" action="{{ route('expenses.destroy', $expense) }}"
                                  onsubmit="return confirm('Delete?')">
                                @csrf @method('DELETE')
                                <button class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 rounded">Del</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No expenses found.</td></tr>
                @endforelse
            </tbody>
        </table>
        @if($expenses->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">{{ $expenses->links() }}</div>
        @endif
    </div>
</div>
@endsection
