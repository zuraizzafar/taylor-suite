<div class="space-y-4" x-data="orderForm()">
    @if(isset($customers))
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Customer') }} *</label>
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
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Branch') }}</label>
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
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Order Date') }} *</label>
            <input type="date" name="order_date" value="{{ old('order_date', isset($order) ? $order->order_date->format('Y-m-d') : date('Y-m-d')) }}"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
            @error('order_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Delivery Date') }} *</label>
            <input type="date" name="delivery_date" value="{{ old('delivery_date', isset($order) ? $order->delivery_date->format('Y-m-d') : '') }}"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
            @error('delivery_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="border border-slate-200 rounded-xl p-4 space-y-3">
        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Base Amount (suits) *</label>
                <input type="number" x-model.number="baseAmount"
                    step="0.01" min="0" placeholder="0"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
            </div>
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Advance') }} *</label>
                <input type="number" name="advance_amount" x-model.number="advanceAmount"
                    step="0.01" min="0"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    required>
                @error('advance_amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
        </div>

        {{-- Extras / Add-ons --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="text-sm font-medium text-slate-700">Extras / Add-ons</label>
                <button type="button" @click="addExtra()"
                    class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1 rounded-lg">+ Add Extra</button>
            </div>
            <template x-for="(extra, i) in extras" :key="i">
                <div class="flex items-center gap-2 mb-2">
                    <input type="text" :name="'extra_name[' + i + ']'" x-model="extra.name" placeholder="Description (e.g. Embroidery)"
                        class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <input type="number" :name="'extra_price[' + i + ']'" x-model.number="extra.price" placeholder="Price" min="0" step="0.01"
                        class="w-32 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <button type="button" @click="removeExtra(i)"
                        class="text-red-400 hover:text-red-600 px-2 py-1.5 rounded">✕</button>
                </div>
            </template>
        </div>

        {{-- Totals summary --}}
        <div class="bg-slate-50 rounded-lg px-4 py-3 grid grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-slate-500 text-xs block">Extras Total</span>
                <span class="font-semibold text-slate-700">Rs <span x-text="extrasTotal.toLocaleString()"></span></span>
            </div>
            <div>
                <span class="text-slate-500 text-xs block">Total Amount</span>
                <span class="font-bold text-slate-900">Rs <span x-text="totalAmount.toLocaleString()"></span></span>
                <input type="hidden" name="total_amount" :value="totalAmount">
                @error('total_amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
            </div>
            <div>
                <span class="text-slate-500 text-xs block">Balance</span>
                <span class="font-bold" :class="balance > 0 ? 'text-red-600' : 'text-green-600'">Rs <span x-text="balance.toLocaleString()"></span></span>
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Notes') }}</label>
        <textarea name="notes" rows="2"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $order->notes ?? '') }}</textarea>
    </div>
</div>

@once
<script>
function orderForm() {
    const existingExtras = @json(isset($order) ? ($order->extras ?? []) : []);
    const existingTotal  = {{ (float) old('total_amount', isset($order) ? $order->total_amount : 0) }};
    const existingAdv    = {{ (float) old('advance_amount', isset($initialAdvance) ? $initialAdvance : (isset($order) ? $order->advance_amount : 0)) }};
    const extrasSum      = existingExtras.reduce((s, e) => s + (parseFloat(e.price) || 0), 0);
    return {
        extras:        existingExtras.map(e => ({ name: e.name, price: parseFloat(e.price) || 0 })),
        baseAmount:    existingTotal - extrasSum,
        advanceAmount: existingAdv,
        get extrasTotal() { return this.extras.reduce((s, e) => s + (parseFloat(e.price) || 0), 0); },
        get totalAmount()  { return Math.max(0, (parseFloat(this.baseAmount) || 0) + this.extrasTotal); },
        get balance()      { return Math.max(0, this.totalAmount - (parseFloat(this.advanceAmount) || 0)); },
        addExtra()    { this.extras.push({ name: '', price: 0 }); },
        removeExtra(i){ this.extras.splice(i, 1); },
    };
}
</script>
@endonce
