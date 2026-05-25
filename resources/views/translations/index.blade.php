@extends('layouts.app')
@section('title', __('Translations'))
@section('page-title', __('Translation Management'))

@section('content')
<div class="pt-2" x-data="translationManager()">

    {{-- Locale Tabs --}}
    <div class="flex gap-2 mb-5">
        <button @click="locale = 'ur'" :class="locale === 'ur' ? 'bg-blue-600 text-white' : 'bg-white text-slate-700 border border-slate-300'"
            class="px-4 py-2 rounded-lg text-sm font-medium">اردو (Urdu)</button>
        <button @click="locale = 'en'" :class="locale === 'en' ? 'bg-blue-600 text-white' : 'bg-white text-slate-700 border border-slate-300'"
            class="px-4 py-2 rounded-lg text-sm font-medium">English (EN)</button>
        <input type="text" x-model="search" placeholder="{{ __('Search') }}..."
            class="ms-auto text-sm border border-slate-300 rounded-lg px-3 py-2 w-64 focus:outline-none focus:ring-2 focus:ring-blue-500">
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium w-1/2">{{ __('English Key') }}</th>
                    <th class="px-4 py-3 text-left font-medium w-1/2">{{ __('Translation') }}</th>
                    <th class="px-4 py-3 text-left font-medium w-28">{{ __('Actions') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100">
                <template x-for="row in filtered" :key="row.key">
                    <tr class="hover:bg-slate-50">
                        <td class="px-4 py-2 text-slate-700 font-mono text-xs" x-text="row.key"></td>
                        <td class="px-4 py-2">
                            <input type="text" x-model="row.value"
                                class="w-full border border-slate-200 rounded px-2 py-1 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
                                :dir="locale === 'ur' ? 'rtl' : 'ltr'"
                                @change="markDirty(row)">
                        </td>
                        <td class="px-4 py-2">
                            <div class="flex gap-1">
                                <button @click="save(row)"
                                    :class="row.dirty ? 'bg-blue-600 text-white' : 'bg-slate-100 text-slate-500'"
                                    class="text-xs px-2 py-1 rounded hover:opacity-80">{{ __('Save') }}</button>
                                <button @click="reset(row)" x-show="row.overridden"
                                    class="text-xs bg-red-50 text-red-600 px-2 py-1 rounded hover:bg-red-100">{{ __('Reset') }}</button>
                            </div>
                        </td>
                    </tr>
                </template>
                <tr x-show="filtered.length === 0">
                    <td colspan="3" class="px-4 py-8 text-center text-slate-400">{{ __('No results found.') }}</td>
                </tr>
            </tbody>
        </table>
    </div>

    {{-- Toast --}}
    <div x-show="toast" x-transition
        class="fixed bottom-4 right-4 bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg text-sm"
        x-text="toast"></div>
</div>

<script>
function translationManager() {
    return {
        locale: 'ur',
        search: '',
        toast: '',
        rows: @json($rows),
        overrides: @json($overrides),

        get filtered() {
            const q = this.search.toLowerCase();
            return this.rows[this.locale]?.filter(r =>
                !q || r.key.toLowerCase().includes(q) || r.value.toLowerCase().includes(q)
            ) ?? [];
        },

        markDirty(row) { row.dirty = true; },

        async save(row) {
            const res = await fetch('{{ route('translations.update') }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ locale: this.locale, key: row.key, value: row.value })
            });
            if (res.ok) {
                row.dirty = false;
                row.overridden = true;
                this.showToast('{{ __('Saved') }}');
            }
        },

        async reset(row) {
            const res = await fetch('{{ route('translations.destroy') }}', {
                method: 'DELETE',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name=csrf-token]').content
                },
                body: JSON.stringify({ locale: this.locale, key: row.key })
            });
            if (res.ok) {
                row.overridden = false;
                row.value = row.default;
                row.dirty = false;
                this.showToast('{{ __('Reset to default') }}');
            }
        },

        showToast(msg) {
            this.toast = msg;
            setTimeout(() => this.toast = '', 2500);
        }
    };
}
</script>
@endsection
