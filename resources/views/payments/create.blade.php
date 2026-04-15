@extends('layouts.app')
@section('title', 'Add Payment')
@section('page-title', 'Add Payment')

@section('content')
<div class="pt-2 max-w-2xl">

    @if(session('error'))
        <div class="mb-4 bg-red-50 border border-red-200 text-red-700 text-sm rounded-xl px-4 py-3">{{ session('error') }}</div>
    @endif
    @if(session('success'))
        <div class="mb-4 bg-green-50 border border-green-200 text-green-700 text-sm rounded-xl px-4 py-3">{{ session('success') }}</div>
    @endif

    <!-- ── Search Panel ─────────────────────────────────────────── -->
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6 mb-4">
        <h2 class="text-sm font-semibold text-slate-700 mb-3">Find Order</h2>

        <div class="relative">
            <input
                id="paySearchInput"
                type="text"
                placeholder="Search by customer name, mobile, file number, suit code, or order number…"
                autocomplete="off"
                class="w-full border border-slate-300 rounded-xl px-4 py-2.5 text-sm pr-10 focus:outline-none focus:ring-2 focus:ring-blue-500"
            >
            <span id="paySearchSpinner" class="hidden absolute right-3 top-2.5 text-slate-400 text-xs animate-pulse">searching…</span>
        </div>

        <!-- Results -->
        <div id="paySearchResults" class="mt-3 space-y-1 max-h-80 overflow-y-auto hidden"></div>
    </div>

    <!-- ── Selected Order + Payment Form ────────────────────────── -->
    <div id="payFormSection" class="{{ $selectedOrder ? '' : 'hidden' }}">
        <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">

            <!-- Order Summary -->
            <div id="orderSummary" class="mb-5 pb-4 border-b border-slate-100">
                @if($selectedOrder)
                    @php $so = $selectedOrder; @endphp
                    <div class="flex items-start justify-between">
                        <div>
                            <p class="text-xs text-slate-500 mb-0.5">Selected Order</p>
                            <p class="font-semibold text-slate-800" id="summaryOrderNum">{{ $so->order_number }}</p>
                            <p class="text-sm text-slate-600" id="summaryCustomer">{{ $so->customer->name }}
                                @if($so->customer->mobile)<span class="text-slate-400">· {{ $so->customer->mobile }}</span>@endif
                            </p>
                        </div>
                        <div class="text-right">
                            <p class="text-xs text-slate-500">Outstanding Balance</p>
                            <p class="text-xl font-bold text-red-600" id="summaryBalance">Rs {{ number_format($so->balance_amount) }}</p>
                            <p class="text-xs text-slate-400">of Rs <span id="summaryTotal">{{ number_format($so->total_amount) }}</span> total</p>
                        </div>
                    </div>
                    <button onclick="clearOrderSelection()" class="mt-3 text-xs text-blue-600 hover:underline">← Change order</button>
                @endif
            </div>

            <!-- Payment Form -->
            <form id="paymentForm" method="POST"
                action="{{ $selectedOrder ? route('orders.payments.store', $selectedOrder) : '#' }}">
                @csrf
                <input type="hidden" id="selectedOrderId" name="_order_id" value="{{ $selectedOrder?->id }}">

                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Amount (Rs) *</label>
                            <input type="number" name="amount" id="payAmountInput" min="1" step="0.01" required
                                max="{{ $selectedOrder?->balance_amount }}"
                                value="{{ old('amount') }}"
                                placeholder="0"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                            @error('amount')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Method *</label>
                            <select name="method"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                                @foreach(\App\Models\Payment::METHODS as $key => $label)
                                <option value="{{ $key }}" {{ old('method') === $key ? 'selected' : '' }}>{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Payment Date *</label>
                            <input type="date" name="payment_date" required
                                value="{{ old('payment_date', date('Y-m-d')) }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-600 mb-1">Reference</label>
                            <input type="text" name="reference" placeholder="Txn ID, cheque no…"
                                value="{{ old('reference') }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    <div>
                        <label class="block text-xs font-semibold text-slate-600 mb-1">Note</label>
                        <input type="text" name="note" placeholder="Optional note"
                            value="{{ old('note') }}"
                            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    </div>
                </div>

                <div class="mt-6 flex gap-3">
                    <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-lg">
                        Record Payment
                    </button>
                    <button type="button" onclick="clearOrderSelection()"
                        class="bg-slate-100 hover:bg-slate-200 text-slate-700 text-sm font-medium px-5 py-2 rounded-lg">
                        Cancel
                    </button>
                </div>
            </form>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
(function () {
    const input     = document.getElementById('paySearchInput');
    const results   = document.getElementById('paySearchResults');
    const spinner   = document.getElementById('paySearchSpinner');
    const formSec   = document.getElementById('payFormSection');
    const form      = document.getElementById('paymentForm');
    const amountIn  = document.getElementById('payAmountInput');
    const orderSum  = document.getElementById('orderSummary');

    let debounceTimer = null;

    // Auto-focus the search input if no order is pre-selected
    if (formSec.classList.contains('hidden')) {
        input.focus();
    }

    input.addEventListener('input', function () {
        clearTimeout(debounceTimer);
        const q = this.value.trim();
        if (q.length < 2) {
            results.classList.add('hidden');
            results.innerHTML = '';
            spinner.classList.add('hidden');
            return;
        }
        spinner.classList.remove('hidden');
        debounceTimer = setTimeout(() => fetchResults(q), 300);
    });

    async function fetchResults(q) {
        try {
            const res  = await fetch(`{{ route('payments.search') }}?q=${encodeURIComponent(q)}`, {
                headers: { 'X-Requested-With': 'XMLHttpRequest' }
            });
            const data = await res.json();
            spinner.classList.add('hidden');
            renderResults(data, q);
        } catch (e) {
            spinner.classList.add('hidden');
        }
    }

    function highlight(text, q) {
        if (!text) return '';
        const escaped = q.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        return String(text).replace(new RegExp(`(${escaped})`, 'gi'),
            '<mark class="bg-yellow-100 rounded-sm px-0.5">$1</mark>');
    }

    function renderResults(data, q) {
        results.innerHTML = '';

        const orders    = data.orders    || [];
        const customers = data.customers || [];
        const suits     = data.suits     || [];

        if (!orders.length && !customers.length && !suits.length) {
            results.innerHTML = `<p class="text-sm text-slate-400 py-2 px-1">No results found with an outstanding balance.</p>`;
            results.classList.remove('hidden');
            return;
        }

        // ── Orders ──────────────────────────────────────────────
        if (orders.length) {
            results.insertAdjacentHTML('beforeend',
                `<p class="text-xs font-semibold text-slate-500 uppercase tracking-wide px-1 pt-1 pb-0.5">Orders</p>`);
            orders.forEach(o => {
                const el = document.createElement('button');
                el.type = 'button';
                el.className = 'w-full text-left flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-blue-50 border border-transparent hover:border-blue-100 transition-colors';
                el.innerHTML = `
                    <div>
                        <span class="font-semibold text-slate-800 text-sm">${highlight(o.order_number, q)}</span>
                        <span class="text-slate-500 text-sm ml-2">${highlight(o.customer_name, q)}</span>
                        ${o.customer_mobile ? `<span class="text-slate-400 text-xs ml-1">· ${highlight(o.customer_mobile, q)}</span>` : ''}
                        <span class="text-xs text-slate-400 ml-2">· ${o.order_date}</span>
                    </div>
                    <div class="shrink-0 text-right ml-4">
                        <span class="text-red-600 font-bold text-sm">Rs ${Number(o.balance_amount).toLocaleString()}</span>
                        <span class="text-slate-400 text-xs block">balance</span>
                    </div>`;
                el.addEventListener('click', () => selectOrder(o));
                results.appendChild(el);
            });
        }

        // ── Customers with pending orders ─────────────────────
        if (customers.length) {
            results.insertAdjacentHTML('beforeend',
                `<p class="text-xs font-semibold text-slate-500 uppercase tracking-wide px-1 pt-3 pb-0.5">Customers</p>`);
            customers.forEach(c => {
                const el = document.createElement('button');
                el.type = 'button';
                el.className = 'w-full text-left flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-purple-50 border border-transparent hover:border-purple-100 transition-colors';
                el.innerHTML = `
                    <div>
                        <span class="font-semibold text-slate-800 text-sm">${highlight(c.name, q)}</span>
                        ${c.mobile ? `<span class="text-slate-400 text-xs ml-2">${highlight(c.mobile, q)}</span>` : ''}
                        ${c.file_number ? `<span class="text-slate-400 text-xs ml-2">· ${highlight(c.file_number, q)}</span>` : ''}
                    </div>
                    <span class="text-xs text-purple-600 font-medium">View orders →</span>`;
                el.addEventListener('click', () => {
                    window.location.href = `{{ url('/customers') }}/${c.id}`;
                });
                results.appendChild(el);
            });
        }

        // ── Suits ─────────────────────────────────────────────
        if (suits.length) {
            results.insertAdjacentHTML('beforeend',
                `<p class="text-xs font-semibold text-slate-500 uppercase tracking-wide px-1 pt-3 pb-0.5">Suits</p>`);
            suits.forEach(s => {
                const el = document.createElement('button');
                el.type = 'button';
                el.className = 'w-full text-left flex items-center justify-between px-3 py-2.5 rounded-lg hover:bg-green-50 border border-transparent hover:border-green-100 transition-colors';
                el.innerHTML = `
                    <div>
                        <span class="font-semibold text-slate-800 text-sm">${highlight(s.suit_code, q)}</span>
                        <span class="text-slate-500 text-sm ml-2">${s.customer_name}</span>
                        ${s.order_number ? `<span class="text-slate-400 text-xs ml-1">· ${s.order_number}</span>` : ''}
                    </div>
                    <div class="shrink-0 text-right ml-4">
                        <span class="text-red-600 font-bold text-sm">Rs ${Number(s.balance_amount).toLocaleString()}</span>
                        <span class="text-slate-400 text-xs block">balance</span>
                    </div>`;
                el.addEventListener('click', () => selectOrder({
                    id: s.id,
                    order_number: s.order_number,
                    customer_name: s.customer_name,
                    customer_mobile: '',
                    total_amount: s.balance_amount,
                    balance_amount: s.balance_amount,
                    order_date: ''
                }));
                results.appendChild(el);
            });
        }

        results.classList.remove('hidden');
    }

    function selectOrder(o) {
        // Update form action
        form.action = `{{ url('/orders') }}/${o.id}/payments`;

        // Update summary panel
        orderSum.innerHTML = `
            <div class="flex items-start justify-between">
                <div>
                    <p class="text-xs text-slate-500 mb-0.5">Selected Order</p>
                    <p class="font-semibold text-slate-800">${o.order_number}</p>
                    <p class="text-sm text-slate-600">${o.customer_name}
                        ${o.customer_mobile ? `<span class="text-slate-400">· ${o.customer_mobile}</span>` : ''}
                    </p>
                </div>
                <div class="text-right">
                    <p class="text-xs text-slate-500">Outstanding Balance</p>
                    <p class="text-xl font-bold text-red-600">Rs ${Number(o.balance_amount).toLocaleString()}</p>
                    <p class="text-xs text-slate-400">of Rs ${Number(o.total_amount).toLocaleString()} total</p>
                </div>
            </div>
            <button type="button" onclick="clearOrderSelection()" class="mt-3 text-xs text-blue-600 hover:underline">← Change order</button>`;

        // Update amount max
        amountIn.max   = o.balance_amount;
        amountIn.value = '';
        amountIn.focus();

        // Show form section
        formSec.classList.remove('hidden');
        results.classList.add('hidden');
        input.value = '';

        // Scroll into view
        formSec.scrollIntoView({ behavior: 'smooth', block: 'start' });
    }

    // Exposed globally for the "← Change order" button
    window.clearOrderSelection = function () {
        formSec.classList.add('hidden');
        orderSum.innerHTML = '';
        form.action = '#';
        amountIn.max = '';
        input.value = '';
        results.innerHTML = '';
        results.classList.add('hidden');
        input.focus();
    };
})();
</script>
@endpush
