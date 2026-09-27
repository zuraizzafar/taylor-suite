<div class="space-y-4" x-data="orderForm(@js($taxInit))" x-init="watchBranchSelect($el)">
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
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Delivery Date') }}</label>
            <div class="flex items-center gap-1">
                <input type="date" id="delivery_date_input" name="delivery_date"
                    value="{{ old('delivery_date', isset($order) ? $order->delivery_date?->format('Y-m-d') : now()->addDays(10)->format('Y-m-d')) }}"
                    class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="button" onclick="document.getElementById('delivery_date_input').value=''"
                    title="Clear delivery date"
                    class="text-slate-400 hover:text-red-500 px-2 py-2 rounded-lg hover:bg-slate-100 transition">✕</button>
            </div>
            @error('delivery_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
    </div>

    <div class="border border-slate-200 rounded-xl p-4 space-y-3">
        <div class="flex items-center justify-between mb-1">
            <label class="text-sm font-medium text-slate-700">{{ __('Items') }}</label>
            <button type="button" @click="addItem()"
                class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1 rounded-lg">+ {{ __('Add Item') }}</button>
        </div>

        <div class="hidden md:grid grid-cols-12 gap-2 px-1 text-xs font-semibold text-slate-500 uppercase">
            <div class="col-span-6">{{ __('Description') }}</div>
            <div class="col-span-2">{{ __('Qty') }}</div>
            <div class="col-span-3">{{ __('Rate') }}</div>
            <div class="col-span-1"></div>
        </div>

        <template x-for="(item, i) in items" :key="i">
            <div class="grid grid-cols-12 gap-2 items-center">
                <input type="text" :name="'description[' + i + ']'" x-model="item.description"
                    placeholder="{{ __('e.g. Suiting pant coat stitching') }}"
                    class="col-span-6 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="number" :name="'qty[' + i + ']'" x-model.number="item.qty" min="0" step="0.01"
                    class="col-span-2 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <input type="number" :name="'rate[' + i + ']'" x-model.number="item.rate" min="0" step="0.01"
                    class="col-span-3 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <button type="button" @click="removeItem(i)"
                    class="col-span-1 text-red-400 hover:text-red-600 px-2 py-1.5 rounded text-center">✕</button>
            </div>
        </template>
        @error('description')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror

        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Advance') }} (Rs)</label>
            <input type="number" name="advance_amount" x-model.number="advanceAmount"
                step="0.01" min="0"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                placeholder="0">
            @error('advance_amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>

        {{-- Extras / Add-ons --}}
        <div>
            <div class="flex items-center justify-between mb-2">
                <label class="text-sm font-medium text-slate-700">Extras / Add-ons</label>
                <button type="button" @click="addExtra()"
                    class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1 rounded-lg">+ Custom</button>
            </div>
            @if(isset($extraTypes) && $extraTypes->isNotEmpty())
            <div class="mb-2">
                <select onchange="orderFormAddPreset(this)"
                    class="w-full border border-slate-200 bg-slate-50 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Quick-add preset extra —</option>
                    @foreach($extraTypes as $et)
                    <option value="{{ json_encode(['name' => $et->name, 'price' => (float) $et->default_price]) }}">
                        {{ $et->name }} (Rs {{ number_format($et->default_price) }})
                    </option>
                    @endforeach
                </select>
            </div>
            @endif
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

        @include('tax._fields', ['taxEnabled' => $taxInit['enabled'], 'taxLabel' => $taxInit['label'], 'discount' => true])

        {{-- Totals summary --}}
        <div class="bg-slate-50 rounded-lg px-4 py-3 grid grid-cols-4 gap-4 text-sm">
            <div>
                <span class="text-slate-500 text-xs block">{{ __('Items Total') }}</span>
                <span class="font-semibold text-slate-700">Rs <span x-text="itemsTotal.toLocaleString()"></span></span>
            </div>
            <div>
                <span class="text-slate-500 text-xs block">{{ __('Extras Total') }}</span>
                <span class="font-semibold text-slate-700">Rs <span x-text="extrasTotal.toLocaleString()"></span></span>
            </div>
            <div>
                <span class="text-slate-500 text-xs block">{{ __('Total Payable') }}</span>
                <span class="font-bold text-slate-900">Rs <span x-text="totalAmount.toLocaleString()"></span></span>
            </div>
            <div>
                <span class="text-slate-500 text-xs block">{{ __('Balance') }}</span>
                <span class="font-bold" :class="balance > 0 ? 'text-red-600' : 'text-green-600'">Rs <span x-text="balance.toLocaleString()"></span></span>
            </div>
        </div>
    </div>

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
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $order->notes ?? '') }}</textarea>
    </div>
</div>

@once
<script>
function orderForm(taxInit) {
    const existingExtras = @json(isset($order) ? ($order->extras ?? []) : []);
    const existingItems  = @json(isset($order) ? $order->items->map(fn ($i) => ['description' => $i->description, 'qty' => (float) $i->qty, 'rate' => (float) $i->rate])->values() : []);
    const existingTotal  = {{ (float) old('subtotal', isset($order) ? $order->subtotal : 0) }};
    const existingAdv    = {{ (float) old('advance_amount', isset($initialAdvance) ? $initialAdvance : (isset($order) ? $order->advance_amount : 0)) }};
    const extrasSum      = existingExtras.reduce((s, e) => s + (parseFloat(e.price) || 0), 0);
    // Legacy orders saved before line items existed: show their lump sum as a single row.
    const fallbackItems  = [{ description: '', qty: 1, rate: Math.max(0, existingTotal - extrasSum) }];
    return withTax({
        items:         existingItems.length ? existingItems : fallbackItems,
        extras:        existingExtras.map(e => ({ name: e.name, price: parseFloat(e.price) || 0 })),
        advanceAmount: existingAdv,
        get itemsTotal()   { return this.items.reduce((s, i) => s + ((parseFloat(i.qty) || 0) * (parseFloat(i.rate) || 0)), 0); },
        get extrasTotal()  { return this.extras.reduce((s, e) => s + (parseFloat(e.price) || 0), 0); },
        get subtotal()     { return Math.max(0, this.itemsTotal + this.extrasTotal); },
        get totalAmount()  { return this.grandTotal; },
        get balance()      { return Math.max(0, this.totalAmount - (parseFloat(this.advanceAmount) || 0)); },
        addItem()     { this.items.push({ description: '', qty: 1, rate: 0 }); },
        removeItem(i) { if (this.items.length > 1) this.items.splice(i, 1); },
        addExtra()    { this.extras.push({ name: '', price: 0 }); },
        removeExtra(i){ this.extras.splice(i, 1); },
    }, taxInit);
}
function orderFormAddPreset(select) {
    if (!select.value) return;
    const preset = JSON.parse(select.value);
    // find Alpine component and push the extra
    const el = select.closest('[x-data]');
    if (el && el._x_dataStack) {
        el._x_dataStack[0].extras.push({ name: preset.name, price: preset.price });
    }
    select.value = '';
}
</script>
@endonce
