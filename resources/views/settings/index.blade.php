@extends('layouts.app')
@section('title', 'Settings')
@section('page-title', 'Company Settings')

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('settings.update') }}" enctype="multipart/form-data" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Company Information --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">🏢 Company Information</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Company Name</label>
                    <input type="text" name="company_name" value="{{ old('company_name', $settings['company_name'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. The Suit Tailor">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Tagline</label>
                    <input type="text" name="company_tagline" value="{{ old('company_tagline', $settings['company_tagline'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g. Expert Suit Tailoring">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Phone</label>
                    <input type="text" name="company_phone" value="{{ old('company_phone', $settings['company_phone'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="+92 300 1234567">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Email</label>
                    <input type="email" name="company_email" value="{{ old('company_email', $settings['company_email'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="info@example.com">
                </div>
                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                    <textarea name="company_address" rows="2"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Shop #5, Main Market, Lahore">{{ old('company_address', $settings['company_address'] ?? '') }}</textarea>
                </div>
            </div>
        </div>

        {{-- Bank / Payment --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">🏦 Bank & Payment Details</h3>
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Bank Name</label>
                    <input type="text" name="bank_name" value="{{ old('bank_name', $settings['bank_name'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="HBL / Meezan / UBL">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Account Title</label>
                    <input type="text" name="bank_account_title" value="{{ old('bank_account_title', $settings['bank_account_title'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="Muhammad Ali">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Account Number / IBAN</label>
                    <input type="text" name="bank_account_number" value="{{ old('bank_account_number', $settings['bank_account_number'] ?? '') }}"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="PK36MEZN0001234567890123">
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium text-slate-700 mb-1">Invoice Legal Note</label>
                <textarea name="invoice_legal_note" rows="3"
                    class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                    placeholder="Only Bank payment accept hai...">{{ old('invoice_legal_note', $settings['invoice_legal_note'] ?? 'Only Bank payment accept hai. Kisi aur Bank me payment krny me issue hota to shop waly aur company zimydar nahi.') }}</textarea>
                <p class="text-xs text-slate-400 mt-1">This note is printed at the bottom of every invoice.</p>
            </div>
        </div>

        {{-- Logo --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">Company Logo & Payment QR</h3>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                {{-- Logo --}}
                <div>
                    <p class="text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wide">Company Logo</p>
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
                    <p class="text-xs font-semibold text-slate-600 mb-2 uppercase tracking-wide">Payment QR Code</p>
                    <div class="flex items-start gap-4">
                        @if(!empty($settings['payment_qr_path']))
                        <img src="{{ asset('storage/' . $settings['payment_qr_path']) }}" alt="Payment QR"
                            class="h-16 w-16 object-contain border border-slate-200 rounded-lg p-1 flex-shrink-0">
                        @endif
                        <div class="flex-1">
                            <input type="file" name="payment_qr" accept="image/png,image/jpeg,image/webp"
                                class="w-full text-sm text-slate-500 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:bg-green-50 file:text-green-700 hover:file:bg-green-100">
                            <p class="text-xs text-slate-400 mt-1">Upload your bank/JazzCash/EasyPaisa QR. Shown on invoices.</p>
                            @error('payment_qr')<p class="text-xs text-red-600 mt-1">{{ $message }}</p>@enderror
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-lg">
                💾 Save Settings
            </button>
        </div>
    </form>
</div>
@endsection
