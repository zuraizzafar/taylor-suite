<div class="space-y-4">
    @if(auth()->user()->isAdmin())
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Branch</label>
        <select name="branch_id"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">— All / Main —</option>
            @foreach($branches as $b)
            <option value="{{ $b->id }}" {{ old('branch_id', $expense->branch_id ?? '') == $b->id ? 'selected' : '' }}>
                {{ $b->name }}
            </option>
            @endforeach
        </select>
    </div>
    @endif

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Category') }} *</label>
        <select name="category" required
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">— Select —</option>
            @foreach(\App\Models\Expense::CATEGORIES as $key => $label)
            <option value="{{ $key }}" {{ old('category', $expense->category ?? '') === $key ? 'selected' : '' }}>{{ $label }}</option>
            @endforeach
        </select>
        @error('category')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Amount (Rs) *</label>
            <input type="number" name="amount" value="{{ old('amount', $expense->amount ?? '') }}"
                step="0.01" min="0.01"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
            @error('amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Date') }} *</label>
            <input type="date" name="date" value="{{ old('date', isset($expense) ? $expense->date->format('Y-m-d') : date('Y-m-d')) }}"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
        </div>
    </div>

    <div class="border border-slate-200 rounded-lg p-3 space-y-3">
        <p class="text-sm font-medium text-slate-700">{{ __('Input Tax') }} <span class="text-xs text-slate-400 font-normal">({{ __('optional — sales tax paid on this purchase') }})</span></p>
        <div>
            <label class="block text-xs text-slate-500 mb-1">{{ __('Sales tax included in the amount above (Rs)') }}</label>
            <input type="number" name="tax_amount" value="{{ old('tax_amount', $expense->tax_amount ?? '') }}" step="0.01" min="0" placeholder="0"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            @error('tax_amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div class="grid grid-cols-3 gap-3">
            <div>
                <label class="block text-xs text-slate-500 mb-1">{{ __('Supplier') }}</label>
                <input type="text" name="supplier_name" value="{{ old('supplier_name', $expense->supplier_name ?? '') }}"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">{{ __('Supplier NTN / STRN') }}</label>
                <input type="text" name="supplier_ntn" value="{{ old('supplier_ntn', $expense->supplier_ntn ?? '') }}"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label class="block text-xs text-slate-500 mb-1">{{ __('Supplier Invoice No.') }}</label>
                <input type="text" name="supplier_invoice_no" value="{{ old('supplier_invoice_no', $expense->supplier_invoice_no ?? '') }}"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Description') }}</label>
        <input type="text" name="description" value="{{ old('description', $expense->description ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
</div>
