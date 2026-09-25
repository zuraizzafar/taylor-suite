{{-- Quick-add customer modal. Must be included OUTSIDE any <form> — nested <form>
     elements are invalid HTML and browsers silently drop the inner tag, which makes
     this form's inputs submit as part of the outer form instead. --}}
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
            <div style="display:flex;gap:10px;margin-bottom:12px;">
                <div style="flex:1;">
                    <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">{{ __('Company Name') }}</label>
                    <input type="text" name="company_name"
                        style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
                </div>
                <div style="flex:1;">
                    <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">{{ __('NTN') }}</label>
                    <input type="text" name="ntn"
                        style="width:100%;border:1px solid #cbd5e1;border-radius:8px;padding:7px 10px;font-size:13px;outline:none;">
                </div>
            </div>
            <div style="margin-bottom:12px;">
                <label style="display:block;font-size:11px;font-weight:600;color:#475569;margin-bottom:4px;">{{ __('Email') }}</label>
                <input type="email" name="email"
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
        e.stopPropagation();
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
