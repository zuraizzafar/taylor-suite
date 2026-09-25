<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;

class SettingController extends Controller
{
    private const KEYS = [
        'company_name',
        'company_tagline',
        'company_address',
        'company_phone',
        'company_email',
        'company_tax_number',
        'tax_label',
        'tax_registration_no',
        'tax_default_mode',
        'tax_default_rate',
        'tax_rate_quotation',
        'tax_rate_order',
        'tax_rate_fabric_sale',
        'tax_quotation_enabled',
        'tax_order_enabled',
        'tax_fabric_sale_enabled',
        'ceo_name',
        'manager_name',
        'ceo_signature_path',
        'manager_signature_path',
        'company_stamp_path',
        'bank_name',
        'bank_account_title',
        'bank_account_number',
        'invoice_legal_note',
        'invoice_legal_note_en',
        'invoice_legal_note_ur',
        'logo_path',
        'payment_qr_path',
        'predefined_notes_en',
        'predefined_notes_ur',
    ];

    public function index(): View
    {
        $settings = Setting::allKeyed();
        return view('settings.index', compact('settings'));
    }

    public function predefinedNotes(): View
    {
        $settings = Setting::allKeyed();
        return view('settings.predefined_notes', compact('settings'));
    }

    public function updatePredefinedNotes(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'predefined_notes_en' => ['nullable', 'string'],
            'predefined_notes_ur' => ['nullable', 'string'],
        ]);

        Setting::set('predefined_notes_en', $data['predefined_notes_en'] ?? '');
        Setting::set('predefined_notes_ur', $data['predefined_notes_ur'] ?? '');

        return back()->with('success', 'Predefined notes saved successfully.');
    }

    public function update(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'company_name'        => ['nullable', 'string', 'max:150'],
            'company_tagline'     => ['nullable', 'string', 'max:200'],
            'company_address'     => ['nullable', 'string', 'max:300'],
            'company_phone'       => ['nullable', 'string', 'max:50'],
            'company_email'       => ['nullable', 'email', 'max:150'],
            'company_tax_number'  => ['nullable', 'string', 'max:50'],
            'tax_label'           => ['nullable', 'string', 'max:30'],
            'tax_registration_no' => ['nullable', 'string', 'max:50'],
            'tax_default_mode'    => ['nullable', 'in:none,exclusive,inclusive'],
            'tax_default_rate'    => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_rate_quotation'  => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_rate_order'      => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_rate_fabric_sale' => ['nullable', 'numeric', 'min:0', 'max:100'],
            'tax_quotation_enabled'   => ['nullable', 'in:0,1'],
            'tax_order_enabled'       => ['nullable', 'in:0,1'],
            'tax_fabric_sale_enabled' => ['nullable', 'in:0,1'],
            'ceo_name'            => ['nullable', 'string', 'max:150'],
            'manager_name'        => ['nullable', 'string', 'max:150'],
            'ceo_signature'       => ['nullable', 'image', 'max:2048', 'mimes:png,jpg,jpeg,webp'],
            'manager_signature'   => ['nullable', 'image', 'max:2048', 'mimes:png,jpg,jpeg,webp'],
            'company_stamp'       => ['nullable', 'image', 'max:2048', 'mimes:png,jpg,jpeg,webp'],
            'bank_name'           => ['nullable', 'string', 'max:150'],
            'bank_account_title'  => ['nullable', 'string', 'max:150'],
            'bank_account_number' => ['nullable', 'string', 'max:50'],
            'invoice_legal_note'  => ['nullable', 'string', 'max:500'],
            'invoice_legal_note_en' => ['nullable', 'string', 'max:500'],
            'invoice_legal_note_ur' => ['nullable', 'string', 'max:500'],
            'logo'               => ['nullable', 'image', 'max:2048', 'mimes:png,jpg,jpeg,webp'],
            'payment_qr'         => ['nullable', 'image', 'max:2048', 'mimes:png,jpg,jpeg,webp'],
            'predefined_notes_en' => ['nullable', 'string'],
            'predefined_notes_ur' => ['nullable', 'string'],
        ]);

        // Handle logo upload
        if ($request->hasFile('logo')) {
            $oldPath = Setting::get('logo_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            Setting::set('logo_path', $request->file('logo')->store('logo', 'public'));
        }

        // Handle payment QR upload
        if ($request->hasFile('payment_qr')) {
            $oldPath = Setting::get('payment_qr_path');
            if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                Storage::disk('public')->delete($oldPath);
            }
            Setting::set('payment_qr_path', $request->file('payment_qr')->store('logo', 'public'));
        }

        // Signature / stamp uploads
        foreach ([
            'ceo_signature'     => 'ceo_signature_path',
            'manager_signature' => 'manager_signature_path',
            'company_stamp'     => 'company_stamp_path',
        ] as $field => $settingKey) {
            if ($request->hasFile($field)) {
                $oldPath = Setting::get($settingKey);
                if ($oldPath && Storage::disk('public')->exists($oldPath)) {
                    Storage::disk('public')->delete($oldPath);
                }
                Setting::set($settingKey, $request->file($field)->store('logo', 'public'));
            }
        }

        // Save all text settings
        foreach (Arr::except($data, ['logo', 'payment_qr', 'ceo_signature', 'manager_signature', 'company_stamp']) as $key => $value) {
            if ($request->has($key)) {
                Setting::set($key, $value ?? '');
            }
        }

        return back()->with('success', 'Settings saved successfully.');
    }
}
