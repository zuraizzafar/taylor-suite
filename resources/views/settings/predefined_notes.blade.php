@extends('layouts.app')
@section('title', __('Predefined Notes'))
@section('page-title', __('Predefined Notes'))

@section('content')
<div class="max-w-3xl">
    <form method="POST" action="{{ route('settings.predefined-notes.update') }}" class="space-y-6">
        @csrf
        @method('PUT')

        {{-- Predefined Notes Card --}}
        <div class="bg-white border border-slate-200 rounded-xl p-6 space-y-4">
            <h3 class="text-sm font-semibold text-slate-700 border-b border-slate-100 pb-2">📝 {{ __('Manage Predefined Notes Presets') }}</h3>
            <p class="text-xs text-slate-500 mb-4">
                These notes will appear as a dropdown selector next to all text/notes inputs across the app (POS order notes, POS suit notes, POS customer measurement notes, standard orders, suits, customer measurements, and customer profiles).
            </p>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Predefined Notes (English)</label>
                    <textarea name="predefined_notes_en" rows="12"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="e.g.&#10;Urgent Order&#10;Double Stitching&#10;No pockets">{{ old('predefined_notes_en', $settings['predefined_notes_en'] ?? '') }}</textarea>
                    <p class="text-xs text-slate-400 mt-1">Enter one note per line. These will show as quick presets in English view.</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1">Predefined Notes (Urdu) / اردو نوٹس</label>
                    <textarea name="predefined_notes_ur" rows="12" dir="rtl"
                        class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                        placeholder="مثال کے طور پر:&#10;جلدی ہے&#10;ڈبل سلائی&#10;جیب نہیں لگانی">{{ old('predefined_notes_ur', $settings['predefined_notes_ur'] ?? '') }}</textarea>
                    <p class="text-xs text-slate-400 mt-1">Enter one note per line. These will show as quick presets in Urdu view.</p>
                </div>
            </div>
        </div>

        <div class="flex justify-end">
            <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-6 py-2 rounded-lg transition">
                💾 {{ __('Save Predefined Notes') }}
            </button>
        </div>
    </form>
</div>
@endsection
