<div class="space-y-4">
    @if(isset($customers))
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Customer *</label>
        <select name="customer_id" id="customer_id"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            {{ isset($order) ? 'disabled' : 'required' }}>
            <option value="">— Select Customer —</option>
            @foreach($customers as $c)
            <option value="{{ $c->id }}"
                {{ old('customer_id', $order->customer_id ?? $selectedCustomer?->id) == $c->id ? 'selected' : '' }}>
                {{ $c->file_number }} – {{ $c->name }} ({{ $c->mobile }})
            </option>
            @endforeach
        </select>
        @error('customer_id')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
    </div>
    @endif

    @if(auth()->user()->isAdmin() && isset($branches) && $branches->isNotEmpty())
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Branch</label>
        <select name="branch_id"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="">— No branch —</option>
            @foreach($branches as $branch)
            <option value="{{ $branch->id }}"
                {{ old('branch_id', $order->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                {{ $branch->name }}
            </option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Order Date *</label>
            <input type="date" name="order_date" value="{{ old('order_date', isset($order) ? $order->order_date->format('Y-m-d') : date('Y-m-d')) }}"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
            @error('order_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Delivery Date *</label>
            <input type="date" name="delivery_date" value="{{ old('delivery_date', isset($order) ? $order->delivery_date->format('Y-m-d') : '') }}"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
            @error('delivery_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="grid grid-cols-3 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Total Amount *</label>
            <input type="number" name="total_amount" value="{{ old('total_amount', $order->total_amount ?? '') }}"
                step="0.01" min="0"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
            @error('total_amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Advance Paid *</label>
            <input type="number" name="advance_amount" value="{{ old('advance_amount', $initialAdvance ?? ($order->advance_amount ?? 0)) }}"
                step="0.01" min="0"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
            @error('advance_amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">Balance (auto)</label>
            <input type="text" readonly placeholder="Auto calculated"
                class="w-full border border-slate-200 bg-slate-50 rounded-lg px-3 py-2 text-sm text-slate-500">
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
        <textarea name="notes" rows="2"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $order->notes ?? '') }}</textarea>
    </div>
</div>
