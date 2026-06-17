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

        // Save all text settings
        foreach (Arr::except($data, ['logo', 'payment_qr']) as $key => $value) {
            Setting::set($key, $value ?? '');
        }

        return back()->with('success', 'Settings saved successfully.');
    }
}
