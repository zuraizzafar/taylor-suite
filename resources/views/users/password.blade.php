@extends('layouts.app')
@section('title', __('Set Password'))
@section('page-title', __('Set Password'))
@section('content')
<div class="pt-2 max-w-md">

    <div class="mb-4">
        <a href="{{ route('users.index') }}" class="text-sm text-blue-600 hover:underline">← {{ __('Back to Users') }}</a>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <div class="mb-5">
            <p class="text-sm text-slate-500">{{ __('Setting password for:') }}</p>
            <p class="text-base font-semibold text-slate-800">{{ $user->name }}</p>
            <p class="text-xs text-slate-400">{{ $user->email }} &middot; {{ ucfirst(str_replace('_', ' ', $user->role)) }}</p>
        </div>

        <form method="POST" action="{{ route('users.password.update', $user) }}">
            @csrf
            @method('PUT')

            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('New Password') }} *</label>
                    <input type="password" name="password"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required minlength="6" autocomplete="new-password">
                    @error('password')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Confirm Password') }} *</label>
                    <input type="password" name="password_confirmation"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        required minlength="6" autocomplete="new-password">
                </div>
            </div>

            <div class="mt-6 flex gap-3">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-5 py-2 rounded-lg">
                    {{ __('Update Password') }}
                </button>
                <a href="{{ route('users.index') }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-5 py-2 rounded-lg">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>

</div>
@endsection
