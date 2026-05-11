@extends('layouts.app')
@section('title', 'New Order — POS')
@section('page-title', '🛒 New Order')

@push('scripts')
<script>
function posApp() {
    return {
        // ── Customer panel ─────────────────────────────────────────────────
        customerMode: 'search',   // 'search' | 'new' | 'selected'
        searchQuery:  '',
        searchResults: [],
        searching: false,
        customer: null,             // selected customer object
        newCustomer: { name:'', mobile:'', address:'' },

        // ── Measurements ───────────────────────────────────────────────────
        showMeasurements: false,
        meas: {
            q_length:'', q_shoulder:'', q_chest:'', q_waist:'', q_seat:'',
            q_sleeve:'', q_sleeve_width:'', q_collar:'', q_front:'', q_back:'',
            q_armhole:'', q_cuff:'',
            s_length:'', s_waist:'', s_seat:'', s_thigh:'', s_knee:'',
            s_bottom:'', s_crotch:'', s_ankle:'',
            notes:''
        },

        // ── Order details ──────────────────────────────────────────────────
        orderDate:     new Date().toISOString().substring(0,10),
        deliveryDate:  '',
        totalAmount:   0,
        advanceAmount: 0,
        paymentMethod: 'cash',
        orderNotes:    '',

        // ── Suits ──────────────────────────────────────────────────────────
        suits: [],

        get balance() {
            return Math.max(0, parseFloat(this.totalAmount)||0 - parseFloat(this.advanceAmount)||0);
        },

        get suitsCount() { return this.suits.length; },

        // ── Customer search ────────────────────────────────────────────────
        async searchCustomers() {
            if (this.searchQuery.length < 2) { this.searchResults = []; return; }
            this.searching = true;
            try {
                const r = await fetch('/pos/customers/search?q=' + encodeURIComponent(this.searchQuery), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                this.searchResults = await r.json();
            } finally { this.searching = false; }
        },

        selectCustomer(c) {
            this.customer      = c;
            this.customerMode  = 'selected';
            this.searchResults = [];
            this.searchQuery   = '';
            // Pre-fill measurements if we have them
            if (c.measurement) {
                this.showMeasurements = true;
                const m = c.measurement;
                for (const k in this.meas) {
                    this.meas[k] = m[k] ?? '';
                }
            }
        },

        clearCustomer() {
            this.customer     = null;
            this.customerMode = 'search';
            this.meas = Object.fromEntries(Object.keys(this.meas).map(k => [k,'']));
            this.showMeasurements = false;
        },

        // ── Suits ──────────────────────────────────────────────────────────
        addSuit() {
            this.suits.push({
                suit_type:          '',
                fabric_meter:       '2.5',
                fabric_description: '',
                stitch_type_id:     '',
                worker_id:          '',
                notes:              '',
            });
        },

        removeSuit(i) {
            this.suits.splice(i, 1);
        },

        // ── Auto-calculate total from suit count ───────────────────────────
        autoTotal() {
            // Only if total is 0 and stitch types have prices we could sum,
            // but let user set total manually — just a helper
        },

        // ── Validation ────────────────────────────────────────────────────
        get canSubmit() {
            const hasCustomer = this.customerMode === 'selected'
                || (this.customerMode === 'new' && this.newCustomer.name && this.newCustomer.mobile);
            const hasSuit = this.suits.length > 0 && this.suits.every(s => s.suit_type && s.fabric_meter);
            const hasOrder = this.orderDate && this.deliveryDate && this.totalAmount >= 0;
            return hasCustomer && hasSuit && hasOrder;
        },

        // ── Init ──────────────────────────────────────────────────────────
        init() {
            this.addSuit();
            // Delivery date default: 14 days from today
            const d = new Date();
            d.setDate(d.getDate() + 14);
            this.deliveryDate = d.toISOString().substring(0,10);

            @if($preCustomer)
            this.selectCustomer({
                id:          {{ $preCustomer->id }},
                name:        '{{ addslashes($preCustomer->name) }}',
                mobile:      '{{ $preCustomer->mobile }}',
                file_number: '{{ $preCustomer->file_number }}',
                address:     '{{ addslashes($preCustomer->address ?? '') }}',
                measurement: @json($preCustomer->measurements->first()),
            });
            @endif
        },
    };
}
</script>
@endpush

@section('content')
<div x-data="posApp()" x-init="init()" class="pt-1">
<form method="POST" action="{{ route('pos.store') }}" @submit.prevent="$el.submit()">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-5 gap-5 items-start">

    {{-- ════════════════════════════════════════════════════════════════════
         LEFT PANEL — Customer + Measurements
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Customer Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-700">👤 Customer</h3>
                <div class="flex gap-1.5 text-xs">
                    <button type="button"
                        @click="customerMode = 'search'; clearCustomer()"
                        :class="customerMode !== 'new' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                        class="px-2.5 py-1 rounded-full font-medium transition">Search</button>
                    <button type="button"
                        @click="customerMode = 'new'"
                        :class="customerMode === 'new' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                        class="px-2.5 py-1 rounded-full font-medium transition">+ New</button>
                </div>
            </div>

            <div class="p-4">
                {{-- Search mode --}}
                <div x-show="customerMode === 'search'">
                    <div class="relative">
                        <input type="text" x-model="searchQuery" @input.debounce.300ms="searchCustomers()"
                            placeholder="Search by name, mobile, or file no…"
                            class="w-full border border-slate-300 rounded-lg pl-3 pr-8 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <span class="absolute right-2.5 top-2.5 text-slate-400 text-xs" x-show="searching">⟳</span>
                    </div>
                    {{-- Results --}}
                    <div x-show="searchResults.length > 0"
                         class="mt-2 border border-slate-200 rounded-lg overflow-hidden divide-y divide-slate-100">
                        <template x-for="c in searchResults" :key="c.id">
                            <button type="button" @click="selectCustomer(c)"
                                class="w-full text-left px-3 py-2.5 hover:bg-blue-50 transition">
                                <div class="flex items-center gap-2">
                                    <span class="text-xs bg-slate-100 text-slate-500 px-1.5 py-0.5 rounded font-mono" x-text="c.file_number"></span>
                                    <span class="text-sm font-medium text-slate-800" x-text="c.name"></span>
                                </div>
                                <div class="text-xs text-slate-400 mt-0.5" x-text="c.mobile"></div>
                            </button>
                        </template>
                    </div>
                    <p x-show="searchQuery.length > 1 && !searching && searchResults.length === 0"
                       class="text-xs text-slate-400 mt-2 text-center py-2">
                       No customers found. <button type="button" @click="customerMode = 'new'" class="text-blue-600 underline">Create new?</button>
                    </p>
                </div>

                {{-- Selected customer badge --}}
                <div x-show="customerMode === 'selected' && customer" class="space-y-2">
                    <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 flex items-start justify-between">
                        <div>
                            <div class="flex items-center gap-2">
                                <span class="font-semibold text-slate-800 text-sm" x-text="customer?.name"></span>
                                <span class="text-xs bg-blue-100 text-blue-600 px-1.5 py-0.5 rounded font-mono" x-text="customer?.file_number"></span>
                            </div>
                            <div class="text-xs text-slate-500 mt-0.5" x-text="customer?.mobile"></div>
                            <div class="text-xs text-slate-400" x-text="customer?.address" x-show="customer?.address"></div>
                        </div>
                        <button type="button" @click="clearCustomer()"
                            class="text-slate-400 hover:text-red-500 text-lg leading-none">✕</button>
                    </div>
                    <input type="hidden" name="customer_id" :value="customer?.id">
                </div>

                {{-- New customer form --}}
                <div x-show="customerMode === 'new'" class="space-y-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Full Name *</label>
                        <input type="text" name="customer_name" x-model="newCustomer.name" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Customer name">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Mobile *</label>
                        <input type="text" name="customer_mobile" x-model="newCustomer.mobile" required
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="03XX-XXXXXXX">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Address</label>
                        <input type="text" name="customer_address" x-model="newCustomer.address"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Optional">
                    </div>
                    @if(auth()->user()->isAdmin())
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Branch</label>
                        <select name="branch_id" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            <option value="">— Select branch —</option>
                            @foreach($branches as $b)
                            <option value="{{ $b->id }}">{{ $b->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Measurements Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <button type="button"
                @click="showMeasurements = !showMeasurements"
                class="w-full flex items-center justify-between px-4 py-3 text-sm font-semibold text-slate-700 hover:bg-slate-50 transition">
                <span>📏 Measurements <span class="text-xs text-slate-400 font-normal">(optional)</span></span>
                <span x-text="showMeasurements ? '▲' : '▼'" class="text-slate-400 text-xs"></span>
            </button>

            <div x-show="showMeasurements" x-cloak class="px-4 pb-4 space-y-3 border-t border-slate-100">
                <div class="pt-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Qameez / Kameez</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            ['q_length','Length'],['q_shoulder','Shoulder'],['q_chest','Chest'],
                            ['q_waist','Waist'],['q_seat','Seat'],['q_sleeve','Sleeve'],
                            ['q_sleeve_width','Slv Width'],['q_collar','Collar'],['q_front','Front'],
                            ['q_back','Back'],['q_armhole','Armhole'],['q_cuff','Cuff'],
                        ] as [$field,$label])
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">{{ $label }}</label>
                            <input type="number" step="0.5" name="measurement[{{ $field }}]" x-model="meas.{{ $field }}"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400"
                                placeholder="—">
                        </div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-2">Shalwar / Trouser</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            ['s_length','Length'],['s_waist','Waist'],['s_seat','Seat'],
                            ['s_thigh','Thigh'],['s_knee','Knee'],['s_bottom','Bottom'],
                            ['s_crotch','Crotch'],['s_ankle','Ankle'],
                        ] as [$field,$label])
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">{{ $label }}</label>
                            <input type="number" step="0.5" name="measurement[{{ $field }}]" x-model="meas.{{ $field }}"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400"
                                placeholder="—">
                        </div>
                        @endforeach
                    </div>
                </div>
                <div>
                    <label class="block text-[10px] text-slate-500 mb-0.5">Notes</label>
                    <input type="text" name="measurement[notes]" x-model="meas.notes"
                        class="w-full border border-slate-200 rounded-md px-2 py-1.5 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400"
                        placeholder="Any special measurement notes">
                </div>
            </div>
        </div>

    </div>{{-- end left --}}

    {{-- ════════════════════════════════════════════════════════════════════
         RIGHT PANEL — Order + Suits
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="lg:col-span-3 space-y-4">

        {{-- Order Details --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4 space-y-3">
            <h3 class="text-sm font-semibold text-slate-700 pb-1 border-b border-slate-100">🧾 Order Details</h3>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Order Date *</label>
                    <input type="date" name="order_date" x-model="orderDate" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Delivery Date *</label>
                    <input type="date" name="delivery_date" x-model="deliveryDate" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Total Amount (Rs) *</label>
                    <input type="number" name="total_amount" x-model="totalAmount" min="0" step="50" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Advance Paid (Rs)</label>
                    <input type="number" name="advance_amount" x-model="advanceAmount" min="0" step="50"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Balance</label>
                    <div class="border border-slate-200 rounded-lg px-3 py-2 text-sm bg-slate-50 font-semibold"
                         :class="balance > 0 ? 'text-red-600' : 'text-green-600'">
                        Rs <span x-text="balance.toLocaleString()"></span>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Payment Method</label>
                    <select name="payment_method" x-model="paymentMethod"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Order Notes</label>
                    <input type="text" name="order_notes" x-model="orderNotes"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Optional">
                </div>
            </div>
        </div>

        {{-- Suits Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="flex items-center justify-between px-4 py-3 border-b border-slate-100">
                <h3 class="text-sm font-semibold text-slate-700">👔 Suits
                    <span class="ml-1.5 bg-blue-100 text-blue-700 text-xs font-bold px-1.5 py-0.5 rounded-full" x-text="suitsCount"></span>
                </h3>
                <button type="button" @click="addSuit()"
                    class="bg-blue-600 hover:bg-blue-700 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition">
                    + Add Suit
                </button>
            </div>

            <div class="divide-y divide-slate-100">
                <template x-for="(suit, idx) in suits" :key="idx">
                    <div class="p-4 space-y-3">
                        {{-- Row 1: type + fabric --}}
                        <div class="flex items-start gap-2">
                            <div class="text-xs font-bold text-slate-400 w-5 pt-2.5 shrink-0" x-text="idx+1"></div>
                            <div class="flex-1 grid grid-cols-2 gap-2">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Suit Type *</label>
                                    <input type="text" :name="'suits['+idx+'][suit_type]'" x-model="suit.suit_type"
                                        required placeholder="Kameez Shalwar, Sherwani…"
                                        list="suit-type-list"
                                        class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">Fabric (m) *</label>
                                    <input type="number" :name="'suits['+idx+'][fabric_meter]'" x-model="suit.fabric_meter"
                                        required min="0.5" step="0.5"
                                        class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                            </div>
                            <button type="button" @click="removeSuit(idx)"
                                x-show="suits.length > 1"
                                class="text-red-400 hover:text-red-600 text-lg leading-none pt-5 shrink-0">✕</button>
                        </div>

                        {{-- Row 2: stitch type + worker --}}
                        <div class="grid grid-cols-2 gap-2 pl-7">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Stitch Type</label>
                                <select :name="'suits['+idx+'][stitch_type_id]'" x-model="suit.stitch_type_id"
                                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">— None —</option>
                                    @foreach($stitchTypes as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }} (Rs {{ number_format($st->base_price,0) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Assign Worker</label>
                                <select :name="'suits['+idx+'][worker_id]'" x-model="suit.worker_id"
                                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">— Unassigned —</option>
                                    @foreach($workers as $w)
                                    <option value="{{ $w->id }}">{{ $w->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                        </div>

                        {{-- Row 3: fabric desc + notes --}}
                        <div class="grid grid-cols-2 gap-2 pl-7">
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Fabric Description</label>
                                <input type="text" :name="'suits['+idx+'][fabric_description]'" x-model="suit.fabric_description"
                                    placeholder="Colour, material…"
                                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">Notes</label>
                                <input type="text" :name="'suits['+idx+'][notes]'" x-model="suit.notes"
                                    placeholder="Special instructions…"
                                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="suits.length === 0"
                     class="py-8 text-center text-sm text-slate-400">
                    No suits added. Click "+ Add Suit" above.
                </div>
            </div>
        </div>

        {{-- Submit Bar --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between gap-4">
                <div class="text-sm text-slate-600">
                    <span x-text="suitsCount + ' suit' + (suitsCount !== 1 ? 's' : '')"></span>
                    <span class="mx-1.5 text-slate-300">·</span>
                    Total: <span class="font-bold text-slate-800">Rs <span x-text="(parseFloat(totalAmount)||0).toLocaleString()"></span></span>
                    <span class="mx-1.5 text-slate-300">·</span>
                    Balance: <span class="font-bold" :class="balance > 0 ? 'text-red-600' : 'text-green-600'">Rs <span x-text="balance.toLocaleString()"></span></span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('orders.index') }}"
                       class="px-4 py-2 text-sm text-slate-600 border border-slate-300 rounded-lg hover:bg-slate-50">
                       Cancel
                    </a>
                    <button type="submit" :disabled="!canSubmit"
                        :class="canSubmit ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer' : 'bg-slate-300 cursor-not-allowed'"
                        class="px-6 py-2 text-sm font-semibold text-white rounded-lg transition">
                        ✓ Create Order
                    </button>
                </div>
            </div>
            <p x-show="!canSubmit" class="text-xs text-amber-600 mt-2">
                Fill in customer, at least one suit (type + fabric), order date, and delivery date to continue.
            </p>
        </div>

    </div>{{-- end right --}}

</div>

{{-- Datalist for common suit types --}}
<datalist id="suit-type-list">
    <option value="Kameez Shalwar">
    <option value="Sherwani">
    <option value="Coat Pant">
    <option value="Waistcoat">
    <option value="Kurta">
    <option value="Shalwar only">
    <option value="Kameez only">
</datalist>

</form>
</div>
@endsection
