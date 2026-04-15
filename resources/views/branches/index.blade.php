@extends('layouts.app')
@section('title', 'Branches')
@section('page-title', 'Branches')
@section('content')
<div class="pt-2">
    <div class="flex justify-end mb-5">
        <a href="{{ route('branches.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + Add Branch
        </a>
    </div>
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Name</th>
                    <th class="px-4 py-3 text-left font-medium">Address</th>
                    <th class="px-4 py-3 text-left font-medium">Phone</th>
                    <th class="px-4 py-3 text-left font-medium">Customers</th>
                    <th class="px-4 py-3 text-left font-medium">Orders</th>
                    <th class="px-4 py-3 text-left font-medium">Status</th>
                    <th class="px-4 py-3 text-left font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($branches as $branch)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $branch->name }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $branch->address ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $branch->phone ?? '—' }}</td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-blue-100 text-blue-700 text-xs px-2 py-0.5 rounded-full">{{ $branch->customers_count }}</span>
                    </td>
                    <td class="px-4 py-3 text-center">
                        <span class="bg-green-100 text-green-700 text-xs px-2 py-0.5 rounded-full">{{ $branch->orders_count }}</span>
                    </td>
                    <td class="px-4 py-3">
                        @if($branch->is_active)
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Active</span>
                        @else
                        <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('branches.edit', $branch) }}"
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded">Edit</a>
                            <form method="POST" action="{{ route('branches.destroy', $branch) }}"
                                  onsubmit="return confirm('Delete this branch?')">
                                @csrf @method('DELETE')
                                <button class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 rounded">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="7" class="px-4 py-8 text-center text-slate-400">No branches yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
