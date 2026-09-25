@once
<script>
// Shared discount + tax maths for Alpine forms. Mirrors App\Services\TaxService::calculate():
// discount comes off the subtotal first, then tax on the remainder, both rounded to whole rupees.
window.taxFmt = function (n) {
    n = Number(n) || 0;
    return n.toLocaleString(undefined, { maximumFractionDigits: 2 });
};

// component must define a numeric `subtotal` getter/property.
window.withTax = function (component, init) {
    init = init || {};
    const mixin = {
        taxEnabled: !!init.enabled,
        taxBranchId: init.branchId || '',
        discountType: init.discountType || '',
        discountValue: init.discountValue || 0,
        taxMode: init.taxMode || 'none',
        taxRate: init.taxRate === undefined || init.taxRate === null ? 0 : init.taxRate,
        taxDefaults: init.defaults || {},

        get discountAmount() {
            const s = Math.max(0, parseFloat(this.subtotal) || 0);
            const v = parseFloat(this.discountValue) || 0;
            if (this.discountType === 'percent') return Math.round(s * Math.min(100, Math.max(0, v)) / 100);
            if (this.discountType === 'fixed')   return Math.min(s, Math.round(Math.max(0, v)));
            return 0;
        },
        get taxableAmount() {
            return Math.max(0, (parseFloat(this.subtotal) || 0) - this.discountAmount);
        },
        get taxAmount() {
            if (!this.taxEnabled || this.taxMode === 'none') return 0;
            const r = Math.max(0, Math.min(100, parseFloat(this.taxRate) || 0));
            return Math.round(this.taxableAmount * r / 100);
        },
        get grandTotal() {
            return this.taxableAmount + (this.taxEnabled && this.taxMode === 'inclusive' ? this.taxAmount : 0);
        },

        taxDefaultFor(branchId) {
            return this.taxDefaults[String(branchId || '')] || this.taxDefaults[''] || null;
        },
        applyTaxDefaults(branchId) {
            this.taxBranchId = branchId || '';
            const d = this.taxDefaultFor(branchId);
            if (!d) return;
            this.taxMode = d.mode;
            this.taxRate = d.rate;
        },
        onTaxModeChange() {
            if (this.taxMode !== 'none' && !(parseFloat(this.taxRate) > 0)) {
                const d = this.taxDefaultFor(this.taxBranchId);
                if (d) this.taxRate = d.rate;
            }
        },
        // Re-apply shop/branch defaults whenever the (admin's) branch dropdown changes.
        watchBranchSelect(root) {
            const sel = root.querySelector('[name=branch_id]');
            if (sel) sel.addEventListener('change', (e) => this.applyTaxDefaults(e.target.value));
        },
    };
    Object.defineProperties(component, Object.getOwnPropertyDescriptors(mixin));
    return component;
};
</script>
@endonce
