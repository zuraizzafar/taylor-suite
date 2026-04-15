@extends('layouts.app')
@section('title', 'User Management')
@section('page-title', 'User Management')
@section('content')
<div class="pt-2">

    <div class="flex justify-end mb-5">
        <a href="{{ route('users.create') }}"
           class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
            + Add User
        </a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Name</th>
                    <th class="px-4 py-3 text-left font-medium">Email</th>
                    <th class="px-4 py-3 text-left font-medium">Role</th>
                    <th class="px-4 py-3 text-left font-medium">Branch</th>
                    <th class="px-4 py-3 text-left font-medium">Actions</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($users as $user)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-medium text-slate-800">{{ $user->name }}</td>
                    <td class="px-4 py-3 text-slate-500">{{ $user->email }}</td>
                    <td class="px-4 py-3">
                        @if($user->role === 'branch_manager')
                        <span class="text-xs bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full">Branch Manager</span>
                        @else
                        <span class="text-xs bg-slate-100 text-slate-600 px-2 py-0.5 rounded-full">Worker</span>
                        @endif
                    </td>
                    <td class="px-4 py-3 text-slate-500">{{ $user->branch?->name ?? '—' }}</td>
                    <td class="px-4 py-3">
                        <a href="{{ route('users.password', $user) }}"
                           class="text-xs bg-amber-50 hover:bg-amber-100 text-amber-700 px-2 py-1 rounded">
                            Set Password
                        </a>
                    </td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">No branch managers or workers found.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>

</div>
@endsection
