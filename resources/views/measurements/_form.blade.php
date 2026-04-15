{{-- Reusable measurement form partial --}}
<div class="space-y-6">
    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Label / Name for this measurement set *</label>
        <input type="text" name="label" value="{{ old('label', $measurement->label ?? 'Default') }}"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500"
            required>
    </div>

    {{-- Qameez --}}
    <div>
        <h4 class="text-sm font-semibold text-slate-700 mb-3 pb-1 border-b border-slate-200">👘 Qameez Measurements (inches)</h4>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach(['q_length' => 'Length', 'q_shoulder' => 'Shoulder', 'q_chest' => 'Chest', 'q_waist' => 'Waist', 'q_seat' => 'Seat', 'q_sleeve' => 'Sleeve', 'q_sleeve_width' => 'Sleeve Width', 'q_collar' => 'Collar', 'q_front' => 'Front', 'q_back' => 'Back', 'q_armhole' => 'Armhole', 'q_cuff' => 'Cuff'] as $field => $label)
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                <input type="number" name="{{ $field }}" value="{{ old($field, $measurement->$field ?? '') }}"
                    step="0.5" min="0"
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            @endforeach
        </div>
    </div>

    {{-- Shalwar --}}
    <div>
        <h4 class="text-sm font-semibold text-slate-700 mb-3 pb-1 border-b border-slate-200">👖 Shalwar Measurements (inches)</h4>
        <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-3">
            @foreach(['s_length' => 'Length', 's_waist' => 'Waist', 's_seat' => 'Seat', 's_thigh' => 'Thigh', 's_knee' => 'Knee', 's_bottom' => 'Bottom', 's_crotch' => 'Crotch', 's_ankle' => 'Ankle'] as $field => $label)
            <div>
                <label class="block text-xs font-medium text-slate-600 mb-1">{{ $label }}</label>
                <input type="number" name="{{ $field }}" value="{{ old($field, $measurement->$field ?? '') }}"
                    step="0.5" min="0"
                    class="w-full border border-slate-300 rounded-lg px-2 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            @endforeach
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

        </div>
    </div>

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
        <textarea name="notes" rows="2"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $measurement->notes ?? '') }}</textarea>
    </div>
</div>
