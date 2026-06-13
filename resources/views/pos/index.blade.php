@extends('layouts.app')
@php
    $locale = app()->getLocale();
    $notesStr = \App\Models\Setting::get("predefined_notes_{$locale}", '');
    $notesList = array_filter(array_map('trim', explode("\n", $notesStr)));
@endphp
@section('title', __('New Order (POS)'))
@section('page-title', '🛒 ' . __('New Order (POS)'))

@push('scripts')
<script>
function posApp() {
    const customerSearchUrl = @json(route('pos.customers.search'));

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
            type: 'shalwar_kameez',
            q_length:'', q_shoulder:'', q_chest:'', q_waist:'', q_seat:'',
            q_sleeve:'', q_sleeve_width:'', q_collar:'', q_front:'', q_back:'',
            q_armhole:'', q_cuff:'',
            s_length:'', s_waist:'', s_seat:'', s_thigh:'', s_knee:'',
            s_bottom:'', s_crotch:'', s_ankle:'',
            notes:'',
            meta: {
                collar_style: '', button_type: '', button_count: '', ghera_style: '',
                stitching_style: '', chak_patti: '', kaj_hale: '', pahuncha_style: '',
                front_patti_size: '', design_number: '', fashion_style: ''
            }
        },

        // ── Order details ──────────────────────────────────────────────────
        orderDate:     new Date().toISOString().substring(0,10),
        deliveryDate:  '',
        baseAmount:    0,
        advanceAmount: 0,
        paymentMethod: 'cash',
        orderNotes:    '',

        // ── Extras / Add-ons ───────────────────────────────────────────────
        extras: [],

        // ── Suits ──────────────────────────────────────────────────────────
        suits: [],

        get extrasTotal() {
            return this.extras.reduce((s, e) => s + (parseFloat(e.price) || 0), 0);
        },

        get totalAmount() {
            return Math.max(0, (parseFloat(this.baseAmount) || 0) + this.extrasTotal);
        },

        get balance() {
            return Math.max(0, this.totalAmount - (parseFloat(this.advanceAmount) || 0));
        },

        get suitsCount() { return this.suits.length; },

        // ── Customer search ────────────────────────────────────────────────
        async searchCustomers() {
            if (this.searchQuery.length < 2) { this.searchResults = []; return; }
            this.searching = true;
            try {
                const r = await fetch(customerSearchUrl + '?q=' + encodeURIComponent(this.searchQuery), {
                    headers: { 'X-Requested-With': 'XMLHttpRequest', 'Accept': 'application/json' }
                });
                if (!r.ok) {
                    throw new Error('Customer search request failed.');
                }
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
                    if (k === 'meta') {
                        const metaObj = m.meta || {};
                        this.meas.meta = {
                            collar_style: metaObj.collar_style ?? '',
                            button_type: metaObj.button_type ?? '',
                            button_count: metaObj.button_count ?? '',
                            ghera_style: metaObj.ghera_style ?? '',
                            stitching_style: metaObj.stitching_style ?? '',
                            chak_patti: metaObj.chak_patti ?? '',
                            kaj_hale: metaObj.kaj_hale ?? '',
                            pahuncha_style: metaObj.pahuncha_style ?? '',
                            front_patti_size: metaObj.front_patti_size ?? '',
                            design_number: metaObj.design_number ?? '',
                            fashion_style: metaObj.fashion_style ?? ''
                        };
                    } else {
                        this.meas[k] = m[k] ?? '';
                    }
                }
                if (!this.meas.type) {
                    this.meas.type = 'shalwar_kameez';
                }
            }
        },

        clearCustomer() {
            this.customer     = null;
            this.customerMode = 'search';
            this.meas = {
                type: 'shalwar_kameez',
                q_length:'', q_shoulder:'', q_chest:'', q_waist:'', q_seat:'',
                q_sleeve:'', q_sleeve_width:'', q_collar:'', q_front:'', q_back:'',
                q_armhole:'', q_cuff:'',
                s_length:'', s_waist:'', s_seat:'', s_thigh:'', s_knee:'',
                s_bottom:'', s_crotch:'', s_ankle:'',
                notes:'',
                meta: {
                    collar_style: '', button_type: '', button_count: '', ghera_style: '',
                    stitching_style: '', chak_patti: '', kaj_hale: '', pahuncha_style: '',
                    front_patti_size: '', design_number: '', fashion_style: ''
                }
            };
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

        addExtra() {
            this.extras.push({ name: '', price: 0 });
        },

        removeExtra(i) {
            this.extras.splice(i, 1);
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
            const hasSuit = this.suits.length > 0 && this.suits.every(s => s.suit_type);
            const total = parseFloat(this.totalAmount) || 0;
            const advance = parseFloat(this.advanceAmount) || 0;
            const hasOrder = this.orderDate && total > 0 && advance <= total;
            return hasCustomer && hasSuit && hasOrder;
        },

        validationError() {
            const total = parseFloat(this.totalAmount) || 0;
            const advance = parseFloat(this.advanceAmount) || 0;

            if (!(this.customerMode === 'selected' || (this.customerMode === 'new' && this.newCustomer.name && this.newCustomer.mobile))) {
                return 'Please select an existing customer or enter a new customer name and mobile number.';
            }

            if (!this.orderDate) {
                return 'Please fill in the order date.';
            }

            if (total <= 0) {
                return 'Total amount must be greater than zero.';
            }

            if (advance > total) {
                return 'Advance cannot be greater than the total amount.';
            }

            if (!(this.suits.length > 0 && this.suits.every(s => s.suit_type))) {
                return 'Please add at least one suit with a suit type.';
            }

            return null;
        },

        submitForm(event) {
            const message = this.validationError();

            if (message) {
                window.alert(message);
                return;
            }

            event.target.submit();
        },

        // ── Init ──────────────────────────────────────────────────────────
        init() {
            this.addSuit();
            // Delivery date default: 10 days from today
            const d = new Date();
            d.setDate(d.getDate() + 10);
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
<div x-data="posApp()" class="pt-1">
<form method="POST" action="{{ route('pos.store') }}" @submit.prevent="submitForm($event)">
@csrf

<div class="grid grid-cols-1 lg:grid-cols-5 gap-5 items-start">

    {{-- ════════════════════════════════════════════════════════════════════
         LEFT PANEL — Customer + Measurements
         ═══════════════════════════════════════════════════════════════════ --}}
    <div class="lg:col-span-2 space-y-4">

        {{-- Customer Card --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm overflow-hidden">
            <div class="px-4 py-3 border-b border-slate-100 flex items-center justify-between">
                <h3 class="text-sm font-semibold text-slate-700">👤 {{ __('Customer') }}</h3>
                <div class="flex gap-1.5 text-xs">
                    <button type="button"
                        @click="customerMode = 'search'; clearCustomer()"
                        :class="customerMode !== 'new' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                        class="px-2.5 py-1 rounded-full font-medium transition">{{ __('Search') }}</button>
                    <button type="button"
                        @click="customer = null; newCustomer = { name:'', mobile:'', address:'' }; customerMode = 'new'"
                        :class="customerMode === 'new' ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500 hover:bg-slate-200'"
                        class="px-2.5 py-1 rounded-full font-medium transition">+ {{ __('New') }}</button>
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
                       No customers found. <button type="button" @click="customer = null; newCustomer = { name:'', mobile:'', address:'' }; customerMode = 'new'" class="text-blue-600 underline">{{ __('Create new?') }}</button>
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
                    <input type="hidden" name="customer_id" :value="customer?.id" :disabled="customerMode !== 'selected' || !customer">
                </div>

                {{-- New customer form --}}
                <div x-show="customerMode === 'new'" class="space-y-2.5">
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Full Name') }} *</label>
                        <input type="text" name="customer_name" x-model="newCustomer.name"
                            :required="customerMode === 'new'"
                            :disabled="customerMode !== 'new'"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Customer name">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Mobile') }} *</label>
                        <input type="text" name="customer_mobile" x-model="newCustomer.mobile"
                            :required="customerMode === 'new'"
                            :disabled="customerMode !== 'new'"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="03XX-XXXXXXX">
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Address') }}</label>
                        <input type="text" name="customer_address" x-model="newCustomer.address"
                            :disabled="customerMode !== 'new'"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                            placeholder="Optional">
                    </div>
                    @if(auth()->user()->isAdmin())
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Branch') }}</label>
                        <select name="branch_id" :disabled="customerMode !== 'new'" class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                <span>📏 {{ __('Measurements') }} <span class="text-xs text-slate-400 font-normal">({{ __('optional') }})</span></span>
                <span x-text="showMeasurements ? '▲' : '▼'" class="text-slate-400 text-xs"></span>
            </button>

            <div x-show="showMeasurements" x-cloak class="px-4 pb-4 space-y-3 border-t border-slate-100">
                
                {{-- Type select --}}
                <div class="pt-3">
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Measurement Type / پیمائش کی قسم</label>
                    <select name="measurement[type]" x-model="meas.type"
                        class="w-full border border-slate-300 bg-white rounded-lg px-2.5 py-1.5 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="shalwar_kameez">Shalwar Kameez (شلوار قمیض)</option>
                        <option value="waistcoat">Waistcoat (واسٹ کوٹ)</option>
                        <option value="pent_coat">Pent Coat (پینٹ کوٹ)</option>
                    </select>
                </div>

                {{-- Type 1: Shalwar Kameez --}}
                <div x-show="meas.type === 'shalwar_kameez'" class="space-y-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">👘 Qameez / Kameez</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            ['q_length','Length (قمیض لمبائی)'],
                            ['q_shoulder','Shoulder (تیرا)'],
                            ['q_collar','Collar (کالر)'],
                            ['q_sleeve','Sleeve (بازو)'],
                            ['q_armhole','Arm Hole (موڈہ)'],
                            ['q_cuff','Cuff /Hole (کف)'],
                            ['q_chest','Chest (چھاتی)'],
                            ['q_waist','Waist (کمر)'],
                            ['q_seat','Hips (قمیض گھیرا)'],
                        ] as [$field,$label])
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">{{ $label }}</label>
                            <input type="number" step="0.5" name="measurement[{{ $field }}]" x-model="meas.{{ $field }}"
                                :disabled="meas.type !== 'shalwar_kameez'"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400"
                                placeholder="—">
                        </div>
                        @endforeach
                    </div>

                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1 pt-1">👖 Shalwar</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            ['s_length','Shalwar Length (شلوار لمبائی)'],
                            ['s_bottom','Bottom (پانچہ)'],
                            ['s_seat','Shalwar Ghera (شلوار گھیرا)'],
                            ['s_crotch','Shalwar Assan (آسن)'],
                        ] as [$field,$label])
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">{{ $label }}</label>
                            <input type="number" step="0.5" name="measurement[{{ $field }}]" x-model="meas.{{ $field }}"
                                :disabled="meas.type !== 'shalwar_kameez'"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400"
                                placeholder="—">
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Type 2: Waistcoat --}}
                <div x-show="meas.type === 'waistcoat'" class="space-y-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">🧥 Waistcoat Details</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            ['q_length','Length (لمبائی)'],
                            ['q_chest','Chest (چھاتی)'],
                            ['q_waist','Waist (کمر)'],
                            ['q_shoulder','Shoulder (تیرا)'],
                            ['q_collar','Collar (کالر)'],
                            ['q_armhole','Arm Hole (موڈہ)'],
                        ] as [$field,$label])
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">{{ $label }}</label>
                            <input type="number" step="0.5" name="measurement[{{ $field }}]" x-model="meas.{{ $field }}"
                                :disabled="meas.type !== 'waistcoat'"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400"
                                placeholder="—">
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Type 3: Pent Coat --}}
                <div x-show="meas.type === 'pent_coat'" class="space-y-3">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1">👔 Coat Details</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            ['q_chest','Chest'],
                            ['q_waist','Waist'],
                            ['q_shoulder','Shoulder'],
                            ['q_back','Crose Back'],
                            ['q_length','Coat Length'],
                            ['q_sleeve','Sleeve'],
                        ] as [$field,$label])
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">{{ $label }}</label>
                            <input type="number" step="0.5" name="measurement[{{ $field }}]" x-model="meas.{{ $field }}"
                                :disabled="meas.type !== 'pent_coat'"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400"
                                placeholder="—">
                        </div>
                        @endforeach
                    </div>

                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide mb-1 pt-1">👖 Pant Details</p>
                    <div class="grid grid-cols-3 gap-2">
                        @foreach([
                            ['s_length','Pant Length'],
                            ['s_crotch','In Side'],
                            ['s_waist','Waist (Pants)'],
                            ['s_seat','Hipps'],
                            ['s_thigh','Thai'],
                            ['s_bottom','Bottom'],
                            ['s_ankle','Back Pocket'],
                        ] as [$field,$label])
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">{{ $label }}</label>
                            <input type="number" step="0.5" name="measurement[{{ $field }}]" x-model="meas.{{ $field }}"
                                :disabled="meas.type !== 'pent_coat'"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400"
                                placeholder="—">
                        </div>
                        @endforeach
                    </div>
                </div>

                {{-- Style & Finishing Options section --}}
                <div class="pt-2 border-t border-slate-100 space-y-2">
                    <p class="text-xs font-semibold text-slate-500 uppercase tracking-wide">✂️ Style & Finishing Options</p>
                    <div class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">Neck / Collar Style</label>
                            <select name="measurement[meta][collar_style]" x-model="meas.meta.collar_style"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 bg-white">
                                <option value="">— Select —</option>
                                @foreach(['Cuff' => 'Cuff', 'Gol Bazoo' => 'Gol Bazoo', 'BAN' => 'BAN', 'Collar' => 'Collar'] as $v => $l)
                                <option value="{{ $v }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">Button Type</label>
                            <select name="measurement[meta][button_type]" x-model="meas.meta.button_type"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 bg-white">
                                <option value="">— Select —</option>
                                @foreach(['Fancy Button' => 'Fancy Button', 'Tech Button' => 'Tech Button (Snap)'] as $v => $l)
                                <option value="{{ $v }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">Number of Buttons</label>
                            <select name="measurement[meta][button_count]" x-model="meas.meta.button_count"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 bg-white">
                                <option value="">— Select —</option>
                                @for($i = 1; $i <= 15; $i++)
                                <option value="{{ $i }}">{{ $i }}</option>
                                @endfor
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">Ghera (Bottom)</label>
                            <select name="measurement[meta][ghera_style]" x-model="meas.meta.ghera_style"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 bg-white">
                                <option value="">— Select —</option>
                                @foreach(['Gol Ghera' => 'Gol Ghera', 'Chauras Ghera' => 'Chauras Ghera (Square)'] as $v => $l)
                                <option value="{{ $v }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">Stitching Style</label>
                            <select name="measurement[meta][stitching_style]" x-model="meas.meta.stitching_style"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 bg-white">
                                <option value="">— Select —</option>
                                @foreach(['Single Silai' => 'Single Silai', 'Double Silai' => 'Double Silai', 'Triple Silai' => 'Triple Silai'] as $v => $l)
                                <option value="{{ $v }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">Chak Patti</label>
                            <select name="measurement[meta][chak_patti]" x-model="meas.meta.chak_patti"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 bg-white">
                                <option value="">— Select —</option>
                                @foreach(['Ghum' => 'Ghum (Hidden)', 'Open' => 'Open'] as $v => $l)
                                <option value="{{ $v }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">Kaj Hale (Buttonhole)</label>
                            <select name="measurement[meta][kaj_hale]" x-model="meas.meta.kaj_hale"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 bg-white">
                                <option value="">— Select —</option>
                                @foreach(['Machine' => 'Machine', 'Hand' => 'Hand'] as $v => $l)
                                <option value="{{ $v }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">Pahuncha (Shalwar Bottom)</label>
                            <select name="measurement[meta][pahuncha_style]" x-model="meas.meta.pahuncha_style"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400 bg-white">
                                <option value="">— Select —</option>
                                @foreach(['Plain' => 'Plain', 'Kadhai Pahuncha' => 'Kadhai (Embroidered)', 'Jali Pahuncha' => 'Jali (Lace/Net)'] as $v => $l)
                                <option value="{{ $v }}">{{ $l }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">Front Patti Size (in)</label>
                            <input type="text" name="measurement[meta][front_patti_size]" x-model="meas.meta.front_patti_size"
                                placeholder="e.g. 1.5"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div>
                            <label class="block text-[10px] text-slate-500 mb-0.5">Design Number</label>
                            <input type="text" name="measurement[meta][design_number]" x-model="meas.meta.design_number"
                                placeholder="e.g. D-01"
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400">
                        </div>
                        <div x-show="meas.type === 'shalwar_kameez'">
                            <label class="block text-[10px] text-slate-500 mb-0.5">Fashion Style / فیشن اسٹائل</label>
                            <input type="text" name="measurement[meta][fashion_style]" x-model="meas.meta.fashion_style"
                                placeholder="e.g. Double pocket..."
                                class="w-full border border-slate-200 rounded-md px-2 py-1 text-xs focus:outline-none focus:ring-1 focus:ring-blue-400">
                        </div>
                    </div>
                </div>

                <div class="notes-container">
                    <div class="flex items-center justify-between mb-0.5">
                        <label class="block text-[10px] text-slate-500">Notes</label>
                        @if(!empty($notesList))
                        <select onchange="selectPredefinedNote(this)" class="text-[9px] border border-slate-300 rounded px-1 py-0.5 bg-slate-50 text-slate-600 focus:outline-none cursor-pointer">
                            <option value="">— Preset —</option>
                            @foreach($notesList as $note)
                            <option value="{{ $note }}">{{ $note }}</option>
                            @endforeach
                            <option value="custom">+ Custom/Clear</option>
                        </select>
                        @endif
                    </div>
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
            <h3 class="text-sm font-semibold text-slate-700 pb-1 border-b border-slate-100">🧾 {{ __('Order Details') }}</h3>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Order Date') }} *</label>
                    <input type="date" name="order_date" x-model="orderDate" required
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Delivery Date') }}</label>
                    <div class="flex items-center gap-1">
                        <input type="date" name="delivery_date" x-model="deliveryDate"
                            class="flex-1 border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="button" @click="deliveryDate = ''"
                            title="Clear delivery date"
                            class="text-slate-400 hover:text-red-500 px-2 py-2 rounded-lg hover:bg-slate-100">✕</button>
                    </div>
                </div>
            </div>

            <div class="grid grid-cols-3 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">Base Amount (Rs) *</label>
                    <input type="number" x-model="baseAmount" min="0" step="50"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="0">
                    <input type="hidden" name="total_amount" :value="totalAmount">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Advance') }} (Rs)</label>
                    <input type="number" name="advance_amount" x-model="advanceAmount" min="0" step="50"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="0">
                </div>
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Balance') }}</label>
                    <div class="border border-slate-200 rounded-lg px-3 py-2 text-sm bg-slate-50 font-semibold"
                         :class="balance > 0 ? 'text-red-600' : 'text-green-600'">
                        Rs <span x-text="balance.toLocaleString()"></span>
                    </div>
                </div>
            </div>

            {{-- Extras / Add-ons --}}
            <div>
                <div class="flex items-center justify-between mb-2">
                    <label class="text-xs font-semibold text-slate-600">Extras / Add-ons <span class="text-slate-400 font-normal">(optional)</span></label>
                    <button type="button" @click="addExtra()"
                        class="text-xs bg-blue-50 hover:bg-blue-100 text-blue-700 px-3 py-1 rounded-lg">+ Custom</button>
                </div>
                @if($extraTypes->isNotEmpty())
                <div class="mb-2">
                    <select onchange="posAddPreset(this)"
                        class="w-full border border-slate-200 bg-slate-50 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
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
                    <div class="flex items-center gap-2 mb-1.5">
                        <input type="text" :name="'extra_name[' + i + ']'" x-model="extra.name" placeholder="Description (e.g. Embroidery)"
                            class="flex-1 border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <input type="number" :name="'extra_price[' + i + ']'" x-model.number="extra.price" placeholder="Rs" min="0" step="50"
                            class="w-24 border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <button type="button" @click="removeExtra(i)" class="text-red-400 hover:text-red-600 px-1.5 py-1 rounded">✕</button>
                    </div>
                </template>
                <div x-show="extrasTotal > 0" class="text-xs text-slate-500 mt-1">
                    Extras total: Rs <span x-text="extrasTotal.toLocaleString()" class="font-semibold text-amber-700"></span>
                    &nbsp;·&nbsp; Order total: Rs <span x-text="totalAmount.toLocaleString()" class="font-semibold text-slate-800"></span>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3">
                <div>
                    <label class="block text-xs font-semibold text-slate-600 mb-1">{{ __('Payment Method') }}</label>
                    <select name="payment_method" x-model="paymentMethod"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        <option value="cash">Cash</option>
                        <option value="bank_transfer">Bank Transfer</option>
                        <option value="cheque">Cheque</option>
                        <option value="online">Online</option>
                    </select>
                </div>
                <div class="notes-container">
                    <div class="flex items-center justify-between mb-1">
                        <label class="block text-xs font-semibold text-slate-600">{{ __('Order Notes') }}</label>
                        @if(!empty($notesList))
                        <select onchange="selectPredefinedNote(this)" class="text-[10px] border border-slate-300 rounded px-1.5 py-0.5 bg-slate-50 text-slate-600 focus:outline-none cursor-pointer">
                            <option value="">— Preset —</option>
                            @foreach($notesList as $note)
                            <option value="{{ $note }}">{{ $note }}</option>
                            @endforeach
                            <option value="custom">+ Custom / Clear</option>
                        </select>
                        @endif
                    </div>
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
                    + {{ __('Add Suit') }}
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
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('Suit Type') }} *</label>
                                    <input type="text" :name="'suits['+idx+'][suit_type]'" x-model="suit.suit_type"
                                        required placeholder="Kameez Shalwar, Sherwani…"
                                        list="suit-type-list"
                                        class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('Fabric Size') }} (meter)</label>
                                    <input type="number" :name="'suits['+idx+'][fabric_meter]'" x-model="suit.fabric_meter"
                                        min="0.5" step="0.5"
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
                                <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('Stitch Type') }}</label>
                                <select :name="'suits['+idx+'][stitch_type_id]'" x-model="suit.stitch_type_id"
                                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                    <option value="">— None —</option>
                                    @foreach($stitchTypes as $st)
                                    <option value="{{ $st->id }}">{{ $st->name }} (Rs {{ number_format($st->base_price,0) }})</option>
                                    @endforeach
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('Assign Worker') }}</label>
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
                                <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('Fabric Description') }}</label>
                                <input type="text" :name="'suits['+idx+'][fabric_description]'" x-model="suit.fabric_description"
                                    placeholder="Colour, material…"
                                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                            <div class="notes-container">
                                <div class="flex items-center justify-between mb-1">
                                    <label class="block text-xs font-semibold text-slate-500">{{ __('Notes') }}</label>
                                    @if(!empty($notesList))
                                    <select onchange="selectPredefinedNote(this)" class="text-[10px] border border-slate-300 rounded px-1.5 py-0.5 bg-slate-50 text-slate-600 focus:outline-none cursor-pointer">
                                        <option value="">— Preset —</option>
                                        @foreach($notesList as $note)
                                        <option value="{{ $note }}">{{ $note }}</option>
                                        @endforeach
                                        <option value="custom">+ Custom / Clear</option>
                                    </select>
                                    @endif
                                </div>
                                <input type="text" :name="'suits['+idx+'][notes]'" x-model="suit.notes"
                                    placeholder="Special instructions…"
                                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            </div>
                        </div>
                    </div>
                </template>

                <div x-show="suits.length === 0"
                     class="py-8 text-center text-sm text-slate-400">
                    {{ __('No suits added.') }}
                </div>
            </div>
        </div>

        {{-- Submit Bar --}}
        <div class="bg-white rounded-xl border border-slate-200 shadow-sm p-4">
            <div class="flex items-center justify-between gap-4">
                <div class="text-sm text-slate-600">
                    <span x-text="suitsCount + ' suit' + (suitsCount !== 1 ? 's' : '')"></span>
                    <span class="mx-1.5 text-slate-300">·</span>
                    Base: <span class="font-semibold text-slate-700">Rs <span x-text="(parseFloat(baseAmount)||0).toLocaleString()"></span></span>
                    <template x-if="extrasTotal > 0">
                        <span> + Extras: <span class="font-semibold text-amber-700">Rs <span x-text="extrasTotal.toLocaleString()"></span></span></span>
                    </template>
                    <span class="mx-1.5 text-slate-300">·</span>
                    {{ __('Total') }}: <span class="font-bold text-slate-800">Rs <span x-text="totalAmount.toLocaleString()"></span></span>
                    <span class="mx-1.5 text-slate-300">·</span>
                    {{ __('Balance') }}: <span class="font-bold" :class="balance > 0 ? 'text-red-600' : 'text-green-600'">Rs <span x-text="balance.toLocaleString()"></span></span>
                </div>
                <div class="flex gap-2">
                    <a href="{{ route('orders.index') }}"
                       class="px-4 py-2 text-sm text-slate-600 border border-slate-300 rounded-lg hover:bg-slate-50">
                       {{ __('Cancel') }}
                    </a>
                    <button type="submit" :disabled="!canSubmit"
                        :class="canSubmit ? 'bg-blue-600 hover:bg-blue-700 cursor-pointer' : 'bg-slate-300 cursor-not-allowed'"
                        class="px-6 py-2 text-sm font-semibold text-white rounded-lg transition">
                        ✓ {{ __('Create Order') }}
                    </button>
                </div>
            </div>
            <p x-show="!canSubmit" class="text-xs text-amber-600 mt-2">
                {{ __('Fill in all required fields to continue.') }}, at least one suit (with suit type), and order date to continue.
            </p>
        </div>

    </div>{{-- end right --}}

</div>

{{-- Datalist for suit types (DB-backed) --}}
<datalist id="suit-type-list">
    @foreach($suitTypes as $name)
    <option value="{{ $name }}">
    @endforeach
</datalist>

<script>
function posAddPreset(select) {
    if (!select.value) return;
    const preset = JSON.parse(select.value);
    const root = select.closest('[x-data]');
    if (root && root._x_dataStack) {
        root._x_dataStack[0].extras.push({ name: preset.name, price: preset.price });
    }
    select.value = '';
}
</script>

</form>
</div>
@if($errors->any())
<script>
window.addEventListener('DOMContentLoaded', function () {
    window.alert(@json($errors->first()));
});
</script>
@endif
@endsection
