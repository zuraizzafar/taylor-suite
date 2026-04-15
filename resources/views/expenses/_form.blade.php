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
        <label class="block text-sm font-medium text-slate-700 mb-1">Category *</label>
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
            <label class="block text-sm font-medium text-slate-700 mb-1">Date *</label>
            <input type="date" name="date" value="{{ old('date', isset($expense) ? $expense->date->format('Y-m-d') : date('Y-m-d')) }}"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Description</label>
        <input type="text" name="description" value="{{ old('description', $expense->description ?? '') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>
</div>
