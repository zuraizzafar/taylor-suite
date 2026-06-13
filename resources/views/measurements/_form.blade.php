{{-- Reusable measurement form partial --}}
<div x-data="{ type: '{{ old('type', $measurement->type ?? 'shalwar_kameez') }}' }" class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Label / Name for this measurement set *</label>
        <input type="text" name="label" value="{{ old('label', $measurement->label ?? 'Default') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Measurement Type / پیمائش کی قسم</label>
        <select name="type" x-model="type"
            class="w-full border border-slate-300 bg-white rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            <option value="shalwar_kameez">Shalwar Kameez (شلوار قمیض)</option>
            <option value="waistcoat">Waistcoat (واسٹ کوٹ)</option>
            <option value="pent_coat">Pent Coat (پینٹ کوٹ)</option>
        </select>
    </div>

    {{-- Type 1: Shalwar Kameez --}}
    <div x-show="type === 'shalwar_kameez'" class="space-y-6">
        {{-- Qameez --}}
        <div>
            <h4 class="text-sm font-semibold text-slate-700 mb-3 pb-1 border-b border-slate-200">👘 Qameez Measurements (inches)</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach([
                    'q_length' => 'Length (قمیض لمبائی)',
                    'q_shoulder' => 'Shoulder (تیرا)',
                    'q_collar' => 'Collar (کالر)',
                    'q_sleeve' => 'Sleeve (بازو)',
                    'q_armhole' => 'Arm Hole (موڈہ)',
                    'q_cuff' => 'Cuff /Hole (کف)',
                    'q_chest' => 'Chest (چھاتی)',
                    'q_waist' => 'Waist (کمر)',
                    'q_seat' => 'Hips (قمیض گھیرا)',
                    'q_sleeve_width' => 'Sleeve Width',
                    'q_front' => 'Front',
                    'q_back' => 'Back'
                ] as $field => $label)
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                    <input type="number" name="{{ $field }}" value="{{ old($field, $measurement->$field ?? '') }}"
                        step="0.5" min="0" :disabled="type !== 'shalwar_kameez'"
                        class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @endforeach
            </div>
        </div>

        {{-- Shalwar --}}
        <div>
            <h4 class="text-sm font-semibold text-slate-700 mb-3 pb-1 border-b border-slate-200">👖 Shalwar Measurements (inches)</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach([
                    's_length' => 'Shalwar Length (شلوار لمبائی)',
                    's_bottom' => 'Bottom (پانچہ)',
                    's_seat' => 'Shalwar Ghera (شلوار گھیرا)',
                    's_crotch' => 'Shalwar Assan (آسن)',
                    's_waist' => 'Waist',
                    's_thigh' => 'Thigh',
                    's_knee' => 'Knee',
                    's_ankle' => 'Ankle'
                ] as $field => $label)
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                    <input type="number" name="{{ $field }}" value="{{ old($field, $measurement->$field ?? '') }}"
                        step="0.5" min="0" :disabled="type !== 'shalwar_kameez'"
                        class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Type 2: Waistcoat --}}
    <div x-show="type === 'waistcoat'" class="space-y-6">
        <div>
            <h4 class="text-sm font-semibold text-slate-700 mb-3 pb-1 border-b border-slate-200">🧥 Waistcoat Measurements (inches)</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach([
                    'q_length' => 'Length (لمبائی)',
                    'q_chest' => 'Chest (چھاتی)',
                    'q_waist' => 'Waist (کمر)',
                    'q_shoulder' => 'Shoulder (تیرا)',
                    'q_collar' => 'Collar (کالر)',
                    'q_armhole' => 'Arm Hole (موڈہ)'
                ] as $field => $label)
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                    <input type="number" name="{{ $field }}" value="{{ old($field, $measurement->$field ?? '') }}"
                        step="0.5" min="0" :disabled="type !== 'waistcoat'"
                        class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Type 3: Pent Coat --}}
    <div x-show="type === 'pent_coat'" class="space-y-6">
        {{-- Coat details --}}
        <div>
            <h4 class="text-sm font-semibold text-slate-700 mb-3 pb-1 border-b border-slate-200">👔 Coat Measurements (inches)</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach([
                    'q_chest' => 'Chest',
                    'q_waist' => 'Waist',
                    'q_shoulder' => 'Shoulder',
                    'q_back' => 'Cross Back',
                    'q_length' => 'Coat Length',
                    'q_sleeve' => 'Sleeve'
                ] as $field => $label)
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                    <input type="number" name="{{ $field }}" value="{{ old($field, $measurement->$field ?? '') }}"
                        step="0.5" min="0" :disabled="type !== 'pent_coat'"
                        class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @endforeach
            </div>
        </div>

        {{-- Pant details --}}
        <div>
            <h4 class="text-sm font-semibold text-slate-700 mb-3 pb-1 border-b border-slate-200">👖 Pant Measurements (inches)</h4>
            <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
                @foreach([
                    's_length' => 'Pant Length',
                    's_crotch' => 'In Side',
                    's_waist' => 'Waist (Pants)',
                    's_seat' => 'Hipps',
                    's_thigh' => 'Thai',
                    's_bottom' => 'Bottom',
                    's_ankle' => 'Back Pocket'
                ] as $field => $label)
                <div>
                    <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                    <input type="number" name="{{ $field }}" value="{{ old($field, $measurement->$field ?? '') }}"
                        step="0.5" min="0" :disabled="type !== 'pent_coat'"
                        class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>
                @endforeach
            </div>
        </div>
    </div>

    {{-- Pakistani Tailoring Style Options --}}
    <div>
        <h4 class="text-sm font-semibold text-slate-700 mb-3 pb-1 border-b border-slate-200">✂️ Style & Finishing Options</h4>
        @php $meta = $measurement->meta ?? []; @endphp
        <div class="grid grid-cols-2 md:grid-cols-3 gap-4">

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Neck / Collar Style</label>
                <select name="meta_collar_style"
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    @foreach(['Cuff' => 'Cuff', 'Gol Bazoo' => 'Gol Bazoo', 'BAN' => 'BAN', 'Collar' => 'Collar'] as $v => $l)
                    <option value="{{ $v }}" {{ old('meta_collar_style', $meta['collar_style'] ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Button Type</label>
                <select name="meta_button_type"
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    @foreach(['Fancy Button' => 'Fancy Button', 'Tech Button' => 'Tech Button (Snap)'] as $v => $l)
                    <option value="{{ $v }}" {{ old('meta_button_type', $meta['button_type'] ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Number of Buttons</label>
                <select name="meta_button_count"
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    @for($i = 1; $i <= 15; $i++)
                    <option value="{{ $i }}" {{ old('meta_button_count', $meta['button_count'] ?? '') == $i ? 'selected' : '' }}>{{ $i }}</option>
                    @endfor
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Ghera (Bottom)</label>
                <select name="meta_ghera_style"
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    @foreach(['Gol Ghera' => 'Gol Ghera', 'Chauras Ghera' => 'Chauras Ghera (Square)'] as $v => $l)
                    <option value="{{ $v }}" {{ old('meta_ghera_style', $meta['ghera_style'] ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Stitching Style</label>
                <select name="meta_stitching_style"
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    @foreach(['Single Silai' => 'Single Silai', 'Double Silai' => 'Double Silai', 'Triple Silai' => 'Triple Silai'] as $v => $l)
                    <option value="{{ $v }}" {{ old('meta_stitching_style', $meta['stitching_style'] ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Chak Patti</label>
                <select name="meta_chak_patti"
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    @foreach(['Ghum' => 'Ghum (Hidden)', 'Open' => 'Open'] as $v => $l)
                    <option value="{{ $v }}" {{ old('meta_chak_patti', $meta['chak_patti'] ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Kaj Hale (Buttonhole)</label>
                <select name="meta_kaj_hale"
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    @foreach(['Machine' => 'Machine', 'Hand' => 'Hand'] as $v => $l)
                    <option value="{{ $v }}" {{ old('meta_kaj_hale', $meta['kaj_hale'] ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Pahuncha (Shalwar Bottom)</label>
                <select name="meta_pahuncha_style"
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                    <option value="">— Select —</option>
                    @foreach(['Plain' => 'Plain', 'Kadhai Pahuncha' => 'Kadhai (Embroidered)', 'Jali Pahuncha' => 'Jali (Lace/Net)'] as $v => $l)
                    <option value="{{ $v }}" {{ old('meta_pahuncha_style', $meta['pahuncha_style'] ?? '') === $v ? 'selected' : '' }}>{{ $l }}</option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Front Patti Size (in)</label>
                <input type="text" name="meta_front_patti_size" value="{{ old('meta_front_patti_size', $meta['front_patti_size'] ?? '') }}"
                    placeholder="e.g. 1.5"
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">Design Number</label>
                <input type="text" name="meta_design_number" value="{{ old('meta_design_number', $meta['design_number'] ?? '') }}"
                    placeholder="e.g. D-01"
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div x-show="type === 'shalwar_kameez'">
                <label class="block text-xs font-medium text-slate-600 mb-1">Fashion Style / فیشن اسٹائل</label>
                <input type="text" name="meta_fashion_style" value="{{ old('meta_fashion_style', $meta['fashion_style'] ?? '') }}"
                    placeholder="e.g. Double pocket..."
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

        </div>
    </div>

    <div class="notes-container">
        <div class="flex items-center justify-between mb-1">
            <label class="block text-sm font-medium text-slate-700">{{ __('Notes') }}</label>
            @php
                $locale = app()->getLocale();
                $notesStr = \App\Models\Setting::get("predefined_notes_{$locale}", '');
                $notesList = array_filter(array_map('trim', explode("\n", $notesStr)));
            @endphp
            @if(!empty($notesList))
            <select onchange="selectPredefinedNote(this)" class="text-xs border border-slate-300 rounded px-2 py-0.5 bg-slate-50 text-slate-600 focus:outline-none cursor-pointer">
                <option value="">— Preset Notes —</option>
                @foreach($notesList as $note)
                <option value="{{ $note }}">{{ $note }}</option>
                @endforeach
                <option value="custom">+ Custom / Clear</option>
            </select>
            @endif
        </div>
        <textarea name="notes" rows="2"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $measurement->notes ?? '') }}</textarea>
    </div>
</div>
