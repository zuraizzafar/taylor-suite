<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Full Name') }} *</label>
        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
        @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Mobile') }} *</label>
        <input type="text" name="mobile" value="{{ old('mobile', $customer->mobile ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
        @error('mobile')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Address') }}</label>
        <input type="text" name="address" value="{{ old('address', $customer->address ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    @if(auth()->user()->isAdmin() && isset($branches) && $branches->isNotEmpty())
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Branch') }}</label>
        <select name="branch_id"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">— {{ __('No branch / All') }} —</option>
            @foreach($branches as $branch)
            <option value="{{ $branch->id }}"
                {{ old('branch_id', $customer->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                {{ $branch->name }}
            </option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="notes-container">
        <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-medium text-slate-700">{{ __('Notes') }}</label>
            @php
                $locale = app()->getLocale();
                $notesStr = \App\Models\Setting::get("predefined_notes_{$locale}", '');
                $notesList = array_filter(array_map('trim', explode("\n", $notesStr)));
            @endphp
            @if(!empty($notesList))
            <select onchange="selectPredefinedNote(this)" class="text-xs border border-slate-300 rounded px-2 py-0.5 bg-slate-50 text-slate-600 focus:outline-none cursor-pointer">
                <option value="">— Preset Notes —</option>
                @foreach($notesList as $note)
                <option value="{{ $note }}">{{ $note }}</option>
                @endforeach
                <option value="custom">+ Custom / Clear</option>
            </select>
            @endif
        </div>
        <textarea name="notes" rows="2"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $customer->notes ?? '') }}</textarea>
    </div>
</div>
