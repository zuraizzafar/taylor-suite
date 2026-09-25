@extends('layouts.app')
@section('title', __('Settings'))
@section('page-title', __('Settings'))

@section('content')
<div class="max-w-3xl">
    @if ($errors->any())
        <div class="bg-red-50 border border-red-200 text-red-600 rounded-lg p-4 mb-6 text-sm">
            <ul class="list-disc pl-5 space-y-1">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Company Information --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">🏢 {{ __('Company Information') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Company Name') }}</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. The Suit Tailor">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Company Tagline') }}</label>
                    <input type="text" name="company_tagline" value="{{ old('company_tagline', $settings['company_tagline'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. Expert Suit Tailoring">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Company Phone') }}</label>
                    <input type="text" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="+92 300 1234567">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Company Email') }}</label>
                    <input type="email" name="company_email" value="{{ old('company_email', $settings['company_email'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="info@example.com">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Tax Registration No. (NTN)') }}</label>
                    <input type="text" name="company_tax_number" value="{{ old('company_tax_number', $settings['company_tax_number'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. G493115">
                    <p class="text-xs text-slate-400 mt-1">{{ __('Prints on invoices and quotations.') }}</p>
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Company Address') }}</label>
                    <textarea name="company_address" rows="2"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Shop #5, Main Market, Lahore">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Tax --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">🧾 {{ __('Tax (GST / Sales Tax)') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Tax Name on Documents') }}</label>
                    <input type="text" name="tax_label" value="{{ old('tax_label', $settings['tax_label'] ?? 'GST') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="GST / Sales Tax">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Sales Tax Registration No. (STRN)') }}</label>
                    <input type="text" name="tax_registration_no" value="{{ old('tax_registration_no', $settings['tax_registration_no'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <p class="text-xs text-slate-400 mt-1">{{ __('Printed beside the NTN. A branch can have its own.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Default Tax Mode') }}</label>
                    @php $dm = old('tax_default_mode', $settings['tax_default_mode'] ?? 'none'); @endphp
                    <select name="tax_default_mode"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        @foreach(\App\Services\TaxService::modeLabels() as $key => $label)
                        <option value="{{ $key }}" {{ $dm === $key ? 'selected' : '' }}>{{ $label }}</option>
                        @endforeach
                    </select>
                    <p class="text-xs text-slate-400 mt-1">{{ __('Can be changed on each quotation, order and fabric sale.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Default Tax Rate (%)') }}</label>
                    <input type="number" name="tax_default_rate" step="0.01" min="0" max="100"
                        value="{{ old('tax_default_rate', $settings['tax_default_rate'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. 18">
                </div>
            </div>

            <div class="rounded-lg bg-slate-50 border border-slate-200 p-3 text-xs text-slate-600 space-y-1">
                <p><strong>{{ __('Exclusive') }}:</strong> {{ __('tax is calculated and printed, but the client pays only the price (you bear the tax).') }}</p>
                <p><strong>{{ __('Inclusive') }}:</strong> {{ __('tax is calculated, printed and added to what the client pays.') }}</p>
                <p>{{ __('Discount is taken off first; tax is calculated on the discounted amount.') }}</p>
            </div>

            <div>
                <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide mb-2">{{ __('Apply tax to') }}</p>
                <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                    @foreach([
                        ['quotation', __('Quotations')],
                        ['order', __('Invoices (Orders)')],
                        ['fabric_sale', __('Fabric Sales')],
                    ] as [$m, $mLabel])
                    <div class="border border-slate-200 rounded-lg p-3 space-y-2">
                        <input type="hidden" name="tax_{{ $m }}_enabled" value="0">
                        <label class="flex items-center gap-2 text-sm font-medium text-slate-700">
                            <input type="checkbox" name="tax_{{ $m }}_enabled" value="1"
                                {{ old("tax_{$m}_enabled", $settings["tax_{$m}_enabled"] ?? '1') !== '0' ? 'checked' : '' }}>
                            {{ $mLabel }}
                        </label>
                        <div>
                            <label class="block text-xs text-slate-500 mb-1">{{ __('Rate (%) — blank uses default') }}</label>
                            <input type="number" name="tax_rate_{{ $m }}" step="0.01" min="0" max="100"
                                value="{{ old("tax_rate_{$m}", $settings["tax_rate_{$m}"] ?? '') }}"
                                class="w-full border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                        </div>
                    </div>
                    @endforeach
                </div>
                <p class="text-xs text-slate-400 mt-2">{{ __('Untick a module to stop tax on it entirely. Branches can override the mode, rate and registration number from the Branches page.') }}</p>
            </div>
        </div>

        {{-- Bank / Payment --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">🏦 {{ __('Bank & Payment Details') }}</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Bank Name') }}</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $settings['bank_name'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="HBL / Meezan / pBL">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Account Title') }}</label>
                    <input type="text" name="bank_account_title" value="{{ old('bank_account_title', $settings['bank_account_title'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Muhammad Ali">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('Account Number') }}</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $settings['bank_account_number'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="PK36MEZN0001234567890123">
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('PAYMENT NOTICE (English)') }}</label>
                    <textarea name="invoice_legal_note_en" rows="3"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Payments are only accepted via the authorised bank account...">{{ old('invoice_legal_note_en', $settings['invoice_legal_note_en'] ?? ($settings['invoice_legal_note'] ?? 'Payments are only accepted via the authorised bank account listed on this invoice. The shop and company are not responsible for any issues arising from payments made to any other account.')) }}</textarea>
                    <p class="text-xs text-slate-400 mt-1">{{ __('Prints on English invoices.') }}</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">{{ __('PAYMENT NOTICE (Urdu)') }}</label>
                    <textarea name="invoice_legal_note_ur" rows="3"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="ادائیگی صرف دیے گئے بینک اکاؤنٹ میں قبول ہوگی...">{{ old('invoice_legal_note_ur', $settings['invoice_legal_note_ur'] ?? ($settings['invoice_legal_note'] ?? 'ادائیگی صرف اس انوائس پر درج مجاز بینک اکاؤنٹ کے ذریعے قبول کی جاتی ہے۔ کسی اور اکاؤنٹ پر کی گئی ادائیگی کی ذمہ داری دکان یا کمپنی پر نہیں ہوگی۔')) }}</textarea>
                    <p class="text-xs text-slate-400 mt-1">{{ __('Prints on Urdu invoices.') }}</p>
                </div>
            </div>
        </div>

        {{-- Logo --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">{{ __('Logo & Payment QR') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Logo --}}
                <div>
                    <p class="text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wide">{{ __('Company Logo') }}</p>
                    <div class="flex items-start gap-4">
                        @if(!empty($settings['logo_path']))
                        <img src="{{ asset('storage/' . $settings['logo_path']) }}" alt="Logo"
                            class="h-16 w-16 object-contain border border-slate-200 rounded-lg p-1 flex-shrink-0">
                        @endif
                        <div class="flex-1">
                            <input type="file" name="logo" accept="image/png,image/jpeg,image/webp"
                                class="w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                            <p class="text-xs text-slate-400 mt-1">PNG/JPG, max 2 MB. Recommended: 200x200 px.</p>
                            @error('logo')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>

                {{-- Payment QR --}}
                <div>
                    <p class="text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wide">{{ __('Payment QR Code') }}</p>
                    <div class="flex items-start gap-4">
                        @if(!empty($settings['payment_qr_path']))
                        <img src="{{ asset('storage/' . $settings['payment_qr_path']) }}" alt="Payment QR"
                            class="h-16 w-16 object-contain border border-slate-200 rounded-lg p-1 flex-shrink-0">
                        @endif
                        <div class="flex-1">
                            <input type="file" name="payment_qr" accept="image/png,image/jpeg,image/webp"
                                class="w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            <p class="text-xs text-slate-400 mt-1">ppload your bank/JazzCash/EasyPaisa QR. Shown on invoices.</p>
                            @error('payment_qr')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Signatories & Stamp (quotations) --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">✍️ {{ __('Signatures & Company Stamp') }}</h3>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                @foreach([
                    ['ceo', 'CEO', 'ceo_name', 'ceo_signature', 'ceo_signature_path'],
                    ['manager', 'Manager', 'manager_name', 'manager_signature', 'manager_signature_path'],
                ] as [$k, $label, $nameKey, $fileKey, $pathKey])
                <div class="space-y-2">
                    <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">{{ __($label) }}</p>
                    <input type="text" name="{{ $nameKey }}" value="{{ old($nameKey, $settings[$nameKey] ?? '') }}"
                        placeholder="{{ __('Full name') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @if(!empty($settings[$pathKey]))
                    <img src="{{ asset('storage/' . $settings[$pathKey]) }}" alt="{{ $label }} signature"
                        class="h-12 object-contain border border-slate-200 rounded-lg p-1 bg-white">
                    @endif
                    <input type="file" name="{{ $fileKey }}" accept="image/png,image/jpeg,image/webp"
                        class="w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                    <p class="text-xs text-slate-400">{{ __('Signature image (PNG with transparent background works best).') }}</p>
                    @error($fileKey)<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
                @endforeach

                <div class="space-y-2">
                    <p class="text-xs font-semibold text-slate-600 uppercase tracking-wide">{{ __('Company Stamp') }}</p>
                    @if(!empty($settings['company_stamp_path']))
                    <img src="{{ asset('storage/' . $settings['company_stamp_path']) }}" alt="Company stamp"
                        class="h-16 w-16 object-contain border border-slate-200 rounded-lg p-1 bg-white">
                    @endif
                    <input type="file" name="company_stamp" accept="image/png,image/jpeg,image/webp"
                        class="w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                    <p class="text-xs text-slate-400">{{ __('Printed beside the signatures on quotations.') }}</p>
                    @error('company_stamp')<p class="text-xs text-red-600">{{ $message }}</p>@enderror
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-lg">
                💾 {{ __('Save Settings') }}
            </button>
        </div>
    </form>
</div>
@endsection
