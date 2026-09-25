{{--
    Discount + tax inputs and a live totals preview.
    Must be rendered inside an Alpine component wrapped with withTax(component, init) —
    it reads: subtotal, discountType, discountValue, taxMode, taxRate, discountAmount,
    taxAmount, grandTotal (see tax/_script.blade.php).

    Params: $taxEnabled (bool), $taxLabel (string), $discount (bool, default true)
--}}
@php
    $discount = $discount ?? true;
    $taxEnabled = $taxEnabled ?? false;
    $taxLabel = $taxLabel ?? 'GST';
@endphp
@include('tax._script')

<div class="space-y-3">
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @if($discount)
        <div class="min-w-0">
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Discount') }}</label>
            <div class="flex gap-2">
                <select name="discount_type" x-model="discountType"
                    class="w-32 shrink-0 border border-slate-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">{{ __('No discount') }}</option>
                    <option value="percent">{{ __('Percent (%)') }}</option>
                    <option value="fixed">{{ __('Fixed (Rs)') }}</option>
                </select>
                <input type="number" name="discount_value" x-model.number="discountValue" x-show="discountType" min="0" step="0.01"
                    :max="discountType === 'percent' ? 100 : null"
                    class="flex-1 min-w-0 w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="0">
            </div>
        </div>
        @endif

        @if($taxEnabled)
        <div class="min-w-0">
            <label class="block text-sm font-medium text-slate-700 mb-1">{{ $taxLabel }}</label>
            <div class="flex gap-2">
                <select name="tax_mode" x-model="taxMode" @change="onTaxModeChange()"
                    class="flex-1 min-w-0 border border-slate-300 rounded-lg px-2 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="none">{{ __('No tax') }}</option>
                    <option value="exclusive">{{ __('Exclusive (not charged)') }}</option>
                    <option value="inclusive">{{ __('Inclusive (charged)') }}</option>
                </select>
                <div class="relative w-24 shrink-0" x-show="taxMode !== 'none'">
                    <input type="number" name="tax_rate" x-model.number="taxRate" min="0" max="100" step="0.01"
                        class="w-full border border-slate-300 rounded-lg pl-3 pr-6 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <span class="absolute right-2 top-2 text-sm text-slate-400">%</span>
                </div>
            </div>
            <p class="text-xs text-slate-400 mt-1" x-show="taxMode === 'exclusive'">{{ __('Tax is printed on the document but the client is not charged for it.') }}</p>
            <p class="text-xs text-slate-400 mt-1" x-show="taxMode === 'inclusive'">{{ __('Tax is printed on the document and added to what the client pays.') }}</p>
        </div>
        @else
        <input type="hidden" name="tax_mode" value="none">
        @endif
    </div>

    <div class="bg-slate-50 rounded-lg px-4 py-3 text-sm space-y-1"
         x-show="discountAmount > 0 || taxMode !== 'none'">
        <div class="flex justify-between text-slate-600">
            <span>{{ __('Subtotal') }}</span>
            <span>Rs <span x-text="taxFmt(subtotal)"></span></span>
        </div>
        <div class="flex justify-between text-rose-600" x-show="discountAmount > 0">
            <span>{{ __('Discount') }}<span x-show="discountType === 'percent'"> (<span x-text="discountValue"></span>%)</span></span>
            <span>− Rs <span x-text="taxFmt(discountAmount)"></span></span>
        </div>
        <div class="flex justify-between" :class="taxMode === 'inclusive' ? 'text-slate-700' : 'text-slate-400'" x-show="taxMode !== 'none'">
            <span>{{ $taxLabel }} (<span x-text="taxRate"></span>%)<span x-show="taxMode === 'exclusive'"> — {{ __('not charged') }}</span></span>
            <span><span x-show="taxMode === 'inclusive'">+ </span>Rs <span x-text="taxFmt(taxAmount)"></span></span>
        </div>
        <div class="flex justify-between font-bold text-slate-900 border-t border-slate-200 pt-1">
            <span>{{ __('Total Payable') }}</span>
            <span>Rs <span x-text="taxFmt(grandTotal)"></span></span>
        </div>
    </div>
</div>
