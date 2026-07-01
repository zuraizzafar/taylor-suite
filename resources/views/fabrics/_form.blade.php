@php $f = $fabric; @endphp
<div class="grid grid-cols-2 gap-4">
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Fabric Type') }} *</label>
        <input type="text" name="fabric_type" value="{{ old('fabric_type', $f->fabric_type ?? '') }}" required
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('fabric_type')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Brand') }}</label>
        <input type="text" name="brand" value="{{ old('brand', $f->brand ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Color') }} *</label>
        <input type="text" name="color" value="{{ old('color', $f->color ?? '') }}" required
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('color')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Design Code') }}</label>
        <input type="text" name="design_code" value="{{ old('design_code', $f->design_code ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Roll Number') }} *</label>
        <input type="text" name="roll_number" value="{{ old('roll_number', $f->roll_number ?? '') }}" required
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('roll_number')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    @if(! $f)
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Total Meter') }} *</label>
        <input type="number" name="total_meter" step="0.01" min="0.1" value="{{ old('total_meter') }}" required
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('total_meter')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    @endif
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Cost Price (Rs/meter)') }} *</label>
        <input type="number" name="cost_price" step="0.01" min="0" value="{{ old('cost_price', $f->cost_price ?? '') }}" required
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('cost_price')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Sale Price (Rs/meter)') }} *</label>
        <input type="number" name="sale_price" step="0.01" min="0" value="{{ old('sale_price', $f->sale_price ?? '') }}" required
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        @error('sale_price')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Supplier') }}</label>
        <input type="text" name="supplier" value="{{ old('supplier', $f->supplier ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Date') }}</label>
        <input type="date" name="purchase_date" value="{{ old('purchase_date', optional($f->purchase_date ?? null)->format('Y-m-d')) }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
    @if(auth()->user()->isAdmin() && $branches->isNotEmpty())
    <div>
        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Branch') }}</label>
        <select name="branch_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">— {{ __('All branches') }} —</option>
            @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ old('branch_id', $f->branch_id ?? '') == $b->id ? 'selected' : '' }}>{{ $b->name }}</option>
            @endforeach
        </select>
    </div>
    @endif
</div>
