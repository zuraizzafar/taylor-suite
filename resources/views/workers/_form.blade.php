<div class="space-y-4">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Worker Name *</label>
        <input type="text" name="name" value="{{ old('name', $worker->name ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
        @error('name')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Mobile</label>
        <input type="text" name="mobile" value="{{ old('mobile', $worker->mobile ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Link to Login User (optional)</label>
        <select name="user_id"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">— none —</option>
            @foreach($users as $user)
            <option value="{{ $user->id }}" {{ old('user_id', $worker->user_id ?? '') == $user->id ? 'selected' : '' }}>
                {{ $user->name }} ({{ $user->email }})
            </option>
            @endforeach
        </select>
        <p class="text-xs text-slate-400 mt-1">Link to a user account with role = worker to allow login.</p>
    </div>

    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" id="is_active" value="1"
            {{ old('is_active', $worker->is_active ?? true) ? 'checked' : '' }}>
        <label for="is_active" class="text-sm text-slate-700">Active</label>
    </div>
</div>
