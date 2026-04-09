@extends('layouts.app')

@section('title', 'Workers')
@section('page-title', 'Workers')

@section('content')
<div class="pt-2">
    <div class="flex justify-end mb-5">
        <a href="{{ route('workers.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + Add Worker
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Name</th>
                    <th class="px-4 py-3 text-left font-medium">Mobile</th>
                    <th class="px-4 py-3 text-left font-medium">Login User</th>
                    <th class="px-4 py-3 text-left font-medium">Status</th>
                    <th class="px-4 py-3 text-left font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($workers as $worker)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $worker->name }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $worker->mobile ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $worker->user?->email ?? '—' }}</td>
                    <td class="px-4 py-3">
                        @if($worker->is_active)
                        <span class="text-xs bg-green-100 text-green-700 px-2 py-0.5 rounded-full">Active</span>
                        @else
                        <span class="text-xs bg-red-100 text-red-700 px-2 py-0.5 rounded-full">Inactive</span>
                        @endif
                    </td>
                    <td class="px-4 py-3">
                        <div class="flex gap-2">
                            <a href="{{ route('workers.edit', $worker) }}"
                               class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded">Edit</a>
                            <form method="POST" action="{{ route('workers.destroy', $worker) }}"
                                  onsubmit="return confirm('Delete this worker?')">
                                @csrf @method('DELETE')
                                <button class="text-xs bg-red-50 hover:bg-red-100 text-red-600 px-2 py-1 rounded">Delete</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No workers yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
