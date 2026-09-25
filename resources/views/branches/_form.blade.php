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

    <div class="border border-slate-200 rounded-lg p-3 space-y-3">
        <p class="text-sm font-medium text-slate-700">{{ __('Tax for this branch') }} <span class="text-xs text-slate-400 font-normal">({{ __('optional — overrides the shop-wide settings') }})</span></p>
        <div class="grid grid-cols-3 gap-3">
            <div class="col-span-3 md:col-span-1">
                <label class="block text-xs text-slate-500 mb-1">{{ __('Tax Mode') }}</label>
                @php $bm = old('tax_mode', $branch->tax_mode ?? ''); @endphp
                <select name="tax_mode"
                    class="w-full border border-slate-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— {{ __('Use shop default') }} —</option>
                    @foreach(\App\Services\TaxService::modeLabels() as $key => $label)
                    <option value="{{ $key }}" {{ $bm === $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">{{ __('Rate (%)') }}</label>
                <input type="number" name="tax_rate" step="0.01" min="0" max="100" value="{{ old('tax_rate', $branch->tax_rate ?? '') }}"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">{{ __('Sales Tax Reg. No.') }}</label>
                <input type="text" name="tax_registration_no" value="{{ old('tax_registration_no', $branch->tax_registration_no ?? '') }}"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    @if(isset($managers) && $managers->isNotEmpty())
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Branch Managers</label>
        <p class="text-xs text-slate-400 mb-2">Select one or more users with the Branch Manager role to assign to this branch.</p>
        <div class="space-y-1 border border-slate-200 rounded-lg p-3 max-h-40 overflow-y-auto">
            @foreach($managers as $mgr)
            <label class="flex items-center gap-2 text-sm cursor-pointer">
                <input type="checkbox" name="manager_ids[]" value="{{ $mgr->id }}"
                    {{ in_array($mgr->id, old('manager_ids', $assignedIds ?? [])) ? 'checked' : '' }}
                    class="rounded border-slate-300">
                <span class="text-slate-700">{{ $mgr->name }}</span>
                <span class="text-xs text-slate-400">({{ $mgr->email }})</span>
                @if($mgr->branch_id && $mgr->branch_id !== ($branch->id ?? null))
                <span class="text-xs text-amber-600">assigned to {{ $mgr->branch?->name }}</span>
                @endif
            </label>
            @endforeach
        </div>
    </div>
    @endif

    <div class="flex items-center gap-2">
        <input type="checkbox" name="is_active" id="is_active" value="1"
            {{ old('is_active', $branch->is_active ?? true) ? 'checked' : '' }}>
        <label for="is_active" class="text-sm text-slate-700">{{ __('Active') }}</label>
    </div>
</div>
