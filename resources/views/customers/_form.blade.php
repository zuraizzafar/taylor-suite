<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Full Name *</label>
        <input type="text" name="name" value="{{ old('name', $customer->name ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
        @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Mobile Number *</label>
        <input type="text" name="mobile" value="{{ old('mobile', $customer->mobile ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
        @error('mobile')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
        <input type="text" name="address" value="{{ old('address', $customer->address ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    @if(auth()->user()->isAdmin() && isset($branches) && $branches->isNotEmpty())
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Branch</label>
        <select name="branch_id"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">— No branch / All —</option>
            @foreach($branches as $branch)
            <option value="{{ $branch->id }}"
                {{ old('branch_id', $customer->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                {{ $branch->name }}
            </option>
            @endforeach
        </select>
    </div>
    @endif

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
        <textarea name="notes" rows="2"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $customer->notes ?? '') }}</textarea>
    </div>
</div>
