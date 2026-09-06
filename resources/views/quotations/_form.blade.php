<div class="space-y-4" x-data="quotationForm()">
    @if(isset($customers))
    <div>
        <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-medium text-slate-700">{{ __('Customer') }} *</label>
            @if(!isset($quotation))
            <button type="button" onclick="openQuickCustomerModal()"
                class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-2 py-1 rounded-lg font-medium">+ {{ __('Add Customer') }}</button>
            @endif
        </div>
        <select name="customer_id" id="customer_id"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            {{ isset($quotation) ? 'disabled' : 'required' }}>
            <option value="">— Select Customer —</option>
            @foreach($customers as $c)
            <option value="{{ $c->id }}"
                {{ old('customer_id', $quotation->customer_id ?? $selectedCustomer?->id) == $c->id ? 'selected' : '' }}>
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
                {{ old('branch_id', $quotation->branch_id ?? '') == $branch->id ? 'selected' : '' }}>
                {{ $branch->name }}
            </option>
            @endforeach
        </select>
    </div>
    @endif

    <div class="grid grid-cols-2 gap-4">
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Quotation Date') }} *</label>
            <input type="date" name="quotation_date"
                value="{{ old('quotation_date', isset($quotation) ? $quotation->quotation_date->format('Y-m-d') : date('Y-m-d')) }}"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
            @error('quotation_date')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
        </div>
        <div>
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Validity (Days)') }} *</label>
            <input type="number" name="validity_days" min="1"
                value="{{ old('validity_days', $quotation->validity_days ?? 15) }}"
                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                required>
            @error('validity_days')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
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

        <div class="grid grid-cols-2 gap-4 pt-2">
            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Advance Required') }} (%)</label>
                <input type="number" name="advance_percentage" x-model.number="advancePct" min="0" max="100" step="1"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
        </div>

        {{-- Totals summary --}}
        <div class="bg-slate-50 rounded-lg px-4 py-3 grid grid-cols-3 gap-4 text-sm">
            <div>
                <span class="text-slate-500 text-xs block">{{ __('Total Quotation Amount') }}</span>
                <span class="font-bold text-slate-900">Rs <span x-text="totalAmount.toLocaleString()"></span></span>
            </div>
            <div>
                <span class="text-slate-500 text-xs block">{{ __('Advance Required') }}</span>
                <span class="font-semibold text-green-700">Rs <span x-text="advanceAmount.toLocaleString()"></span></span>
            </div>
            <div>
                <span class="text-slate-500 text-xs block">{{ __('Remaining Balance') }}</span>
                <span class="font-bold text-amber-700">Rs <span x-text="balance.toLocaleString()"></span></span>
            </div>
        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Design & Embroidery Reference') }}</label>
        <textarea name="design_reference" rows="2"
            placeholder="{{ __('Optional notes describing referenced design/embroidery, e.g. numbered areas in an attached image') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('design_reference', $quotation->design_reference ?? '') }}</textarea>
    </div>

    <div class="notes-container">
        <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Notes') }}</label>
        <textarea name="notes" rows="2"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $quotation->notes ?? '') }}</textarea>
    </div>
</div>

@once
<script>
function quotationForm() {
    const existingItems = @json(isset($quotation) ? $quotation->items->map(fn ($i) => ['description' => $i->description, 'qty' => (float) $i->qty, 'rate' => (float) $i->rate])->values() : []);
    return {
        items: existingItems.length ? existingItems : [{ description: '', qty: 1, rate: 0 }],
        advancePct: {{ (float) old('advance_percentage', $quotation->advance_percentage ?? 50) }},
        get totalAmount() {
            return this.items.reduce((s, i) => s + ((parseFloat(i.qty) || 0) * (parseFloat(i.rate) || 0)), 0);
        },
        get advanceAmount() {
            return Math.round(this.totalAmount * ((parseFloat(this.advancePct) || 0) / 100));
        },
        get balance() { return Math.max(0, this.totalAmount - this.advanceAmount); },
        addItem()    { this.items.push({ description: '', qty: 1, rate: 0 }); },
        removeItem(i){ if (this.items.length > 1) this.items.splice(i, 1); },
    };
}
</script>

{{-- Quick-add customer modal --}}
<div id="qc-overlay" onclick="closeQuickCustomerModal()"
     style="display:none;position:fixed;inset:0;background:rgba(0,0,0,0.55);z-index:9999;align-items:center;justify-content:center;">
    <div onclick="event.stopPropagation()"
         style="background:#fff;border-radius:14px;padding:24px 28px;width:100%;max-width:420px;margin:16px;box-shadow:0 25px 60px rgba(0,0,0,0.3);">
        <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:16px;">
            <h3 style="font-size:16px;font-weight:700;color:#1e293b">{{ __('Add Customer') }}</h3>
            <button type="button" onclick="closeQuickCustomerModal()" style="font-size:18px;color:#94a3b8;background:none;border:none;cursor:pointer;">&times;</button>
        </div>
        <form id="qc-form">
            @csrf
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">{{ __('Full Name') }} *</label>
                <input type="text" name="name" required
                    style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
            </div>
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">{{ __('Mobile') }} *</label>
                <input type="text" name="mobile" required
                    style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
            </div>
            <div style="margin-bottom:16px;">
                <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">{{ __('Address') }}</label>
                <input type="text" name="address"
                    style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
            </div>
            <p id="qc-error" style="display:none;color:#dc2626;font-size:12px;margin-bottom:12px;"></p>
            <div style="display:flex;gap:10px;">
                <button type="submit" id="qc-submit"
                    style="flex:1;background:#2563eb;color:#fff;border:none;border-radius:8px;padding:9px 0;font-size:13px;font-weight:600;cursor:pointer;">
                    ✓ {{ __('Save') }}
                </button>
                <button type="button" onclick="closeQuickCustomerModal()"
                    style="background:#f1f5f9;color:#475569;border:none;border-radius:8px;padding:9px 16px;font-size:13px;font-weight:600;cursor:pointer;">
                    {{ __('Cancel') }}
                </button>
            </div>
        </form>
    </div>
</div>
<script>
function openQuickCustomerModal() {
    document.getElementById('qc-overlay').style.display = 'flex';
    document.getElementById('qc-error').style.display = 'none';
}
function closeQuickCustomerModal() {
    document.getElementById('qc-overlay').style.display = 'none';
}
document.addEventListener('DOMContentLoaded', function () {
    const form = document.getElementById('qc-form');
    if (!form) return;
    form.addEventListener('submit', function (e) {
        e.preventDefault();
        const btn = document.getElementById('qc-submit');
        const errorEl = document.getElementById('qc-error');
        errorEl.style.display = 'none';
        btn.disabled = true;
        btn.textContent = '…';

        fetch('{{ route('customers.quick-create') }}', {
            method: 'POST',
            headers: { 'Accept': 'application/json' },
            body: new FormData(form),
        })
        .then(async (res) => {
            const data = await res.json();
            if (!res.ok) throw data;
            return data;
        })
        .then((customer) => {
            const select = document.getElementById('customer_id');
            const option = new Option(customer.label, customer.id, true, true);
            select.add(option);
            select.dispatchEvent(new Event('change', { bubbles: true }));
            form.reset();
            closeQuickCustomerModal();
        })
        .catch((err) => {
            const msg = err?.errors
                ? Object.values(err.errors).flat().join(' ')
                : (err?.message || 'Could not create customer.');
            errorEl.textContent = msg;
            errorEl.style.display = 'block';
        })
        .finally(() => {
            btn.disabled = false;
            btn.textContent = '✓ {{ __('Save') }}';
        });
    });
});
</script>
@endonce
