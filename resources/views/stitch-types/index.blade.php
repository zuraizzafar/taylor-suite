@extends('layouts.app')
@section('title', 'Stitch Types')
@section('page-title', 'Stitch Types & Pricing')

@section('content')
<div class="pt-2 max-w-4xl">

    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">✅ {{ session('success') }}</div>
    @endif
    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">❌ {{ session('error') }}</div>
    @endif

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

        {{-- ── Add New Stitch Type ────────────────────────────────── --}}
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
            <h2 class="text-sm font-semibold text-slate-700 mb-4">Add Stitch Type</h2>
            <form method="POST" action="{{ route('stitch-types.store') }}">
                @csrf
                <div class="space-y-3">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Name *</label>
                        <input type="text" name="name" value="{{ old('name') }}" placeholder="e.g. Simple, Double Stitching…"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Base Price (Rs) *</label>
                        <input type="number" name="base_price" value="{{ old('base_price', 0) }}" min="0" step="0.01"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500" required>
                        <p class="text-xs text-slate-400 mt-1">Default earning per suit. Workers can have overrides.</p>
                        @error('base_price')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                    </div>
                    @if(auth()->user()->isAdmin() && $branches->isNotEmpty())
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Branch (optional)</label>
                        <select name="branch_id"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">— All branches —</option>
                            @foreach($branches as $b)
                            <option value="{{ $b->id }}" {{ old('branch_id') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                    <div class="flex items-center gap-2">
                        <input type="checkbox" name="is_active" id="is_active_new" value="1" checked>
                        <label for="is_active_new" class="text-sm text-slate-700">Active</label>
                    </div>
                    <button type="submit"
                        class="w-full bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium py-2 rounded-lg">
                        Add Stitch Type
                    </button>
                </div>
            </form>
        </div>

        {{-- ── Stitch Types Table ─────────────────────────────────── --}}
        <div class="lg:col-span-2 bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
            <div class="px-5 py-4 border-b border-slate-100">
                <h2 class="text-sm font-semibold text-slate-700">All Stitch Types</h2>
                <p class="text-xs text-slate-400 mt-0.5">{{ $stitchTypes->count() }} types defined</p>
            </div>

            @if($stitchTypes->isEmpty())
                <p class="text-sm text-slate-400 px-5 py-8 text-center">No stitch types yet. Add one on the left.</p>
            @else
            <div class="divide-y divide-slate-50">
                @foreach($stitchTypes as $st)
                <div x-data="{ editing: false }" class="px-5 py-3">
                    {{-- View row --}}
                    <div x-show="!editing" class="flex items-center justify-between gap-4">
                        <div class="flex items-center gap-3">
                            <span class="w-2 h-2 rounded-full {{ $st->is_active ? 'bg-green-400' : 'bg-slate-300' }}"></span>
                            <div>
                                <p class="text-sm font-medium text-slate-800">{{ $st->name }}</p>
                                @if($st->branch)
                                <p class="text-xs text-slate-400">{{ $st->branch->name }}</p>
                                @else
                                <p class="text-xs text-slate-400">All branches</p>
                                @endif
                            </div>
                        </div>
                        <div class="flex items-center gap-4">
                            <span class="text-sm font-semibold text-slate-700">Rs {{ number_format($st->base_price) }}</span>
                            <button @click="editing = true"
                                class="text-xs text-blue-600 hover:underline">Edit</button>
                            <form method="POST" action="{{ route('stitch-types.destroy', $st) }}"
                                onsubmit="return confirm('Delete {{ $st->name }}?')">
                                @csrf @method('DELETE')
                                <button type="submit" class="text-xs text-red-500 hover:underline">Delete</button>
                            </form>
                        </div>
                    </div>

                    {{-- Edit inline row --}}
                    <div x-show="editing" x-cloak>
                        <form method="POST" action="{{ route('stitch-types.update', $st) }}" class="space-y-3 mt-2">
                            @csrf @method('PUT')
                            <div class="grid grid-cols-2 gap-3">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Name *</label>
                                    <input type="text" name="name" value="{{ $st->name }}" required
                                        class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-600 mb-1">Base Price (Rs) *</label>
                                    <input type="number" name="base_price" value="{{ $st->base_price }}" min="0" step="0.01" required
                                        class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            @if(auth()->user()->isAdmin() && $branches->isNotEmpty())
                            <div>
                                <label class="block text-xs font-semibold text-slate-600 mb-1">Branch</label>
                                <select name="branch_id"
                                    class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">— All branches —</option>
                                    @foreach($branches as $b)
                                    <option value="{{ $b->id }}" {{ $st->branch_id == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            @endif
                            <div class="flex items-center gap-2">
                                <input type="checkbox" name="is_active" id="ia_{{ $st->id }}" value="1" {{ $st->is_active ? 'checked' : '' }}>
                                <label for="ia_{{ $st->id }}" class="text-sm text-slate-700">Active</label>
                            </div>
                            <div class="flex gap-2">
                                <button type="submit"
                                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-medium px-4 py-1.5 rounded-lg">Save</button>
                                <button type="button" @click="editing = false"
                                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-medium px-4 py-1.5 rounded-lg">Cancel</button>
                            </div>
                        </form>
                    </div>
                </div>
                @endforeach
            </div>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
// Alpine.js — needed for x-data inline edit toggle
if (!window.Alpine) {
    const s = document.createElement('script');
    s.src = 'https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js';
    s.defer = true;
    document.head.appendChild(s);
}
</script>
@endpush
@endsection
