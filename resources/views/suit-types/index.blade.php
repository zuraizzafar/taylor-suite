@extends('layouts.app')
@section('title', __('Suit Types'))
@section('page-title', __('Suit Types'))

@section('content')
<div class="pt-2 max-w-4xl">

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">❌ {{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Add New Suit Type ─────────────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">{{ __('Add Suit Type') }}</h2>
            <form method="POST" action="{{ route('suit-types.store') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Name') }} *</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Kameez Shalwar, Sherwani…"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active_new" value="1" checked>
                        <label for="is_active_new" class="text-sm text-slate-700">{{ __('Active') }}</label>
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-lg">
                        {{ __('Add Suit Type') }}
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Suit Types List ───────────────────────────────────────── --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">{{ __('All Suit Types') }}</h2>
                <p class="text-xs text-slate-400 mt-0.5">{{ $suitTypes->count() }} types defined · used as defaults in suit creation</p>
            </div>

            @if($suitTypes->isEmpty())
                <p class="text-sm text-slate-400 px-5 py-8 text-center">{{ __('No suit types found.') }} Add one on the left.</p>
            @else
            <div class="divide-y divide-slate-50">
                @foreach($suitTypes as $st)
                <div x-data="{ editing: false }" class="px-5 py-3">
                    {{-- View row --}}
                    <div x-show="!editing" class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full {{ $st->is_active ? 'bg-green-400' : 'bg-slate-300' }}"></span>
                            <p class="text-sm font-medium text-slate-800">{{ $st->name }}</p>
                        </div>
                        <div class="flex items-center gap-2">
                            <button @click="editing = true"
                                class="text-xs text-blue-600 hover:text-blue-800 px-2 py-1 rounded hover:bg-blue-50">
                                {{ __('Edit') }}
                            </button>
                            <form method="POST" action="{{ route('suit-types.destroy', $st) }}"
                                onsubmit="return confirm('Delete {{ addslashes($st->name) }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:text-red-700 px-2 py-1 rounded hover:bg-red-50">
                                    {{ __('Delete') }}
                                </button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit inline --}}
                    <div x-show="editing" x-cloak>
                        <form method="POST" action="{{ route('suit-types.update', $st) }}" class="flex items-center gap-2">
                            @csrf @method('PUT')
                            <input type="text" name="name" value="{{ $st->name }}"
                                class="flex-1 border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                            <div class="flex items-center gap-1 text-xs text-slate-600">
                                <input type="checkbox" name="is_active" value="1" {{ $st->is_active ? 'checked' : '' }}>
                                <span>Active</span>
                            </div>
                            <button type="submit" class="text-xs bg-blue-600 text-white px-3 py-1.5 rounded-lg hover:bg-blue-700">
                                {{ __('Save') }}
                            </button>
                            <button type="button" @click="editing = false" class="text-xs text-slate-500 hover:text-slate-700 px-2 py-1.5 rounded">
                                {{ __('Cancel') }}
                            </button>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>

    </div>
</div>
@endsection
