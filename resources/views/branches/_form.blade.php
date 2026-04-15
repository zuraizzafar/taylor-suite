<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Branch Name *</label>
        <input type="text" name="name" value="{{ old('name', $branch->name ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
        @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
        <input type="text" name="address" value="{{ old('address', $branch->address ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
        <input type="text" name="phone" value="{{ old('phone', $branch->phone ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" id="is_active" value="1"
            {{ old('is_active', $branch->is_active ?? true) ? 'checked' : '' }}>
        <label for="is_active" class="text-sm text-slate-700">Active</label>
    </div>
</div>
