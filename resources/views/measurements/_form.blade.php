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

    <div>
        <label class="block text-sm font-medium text-slate-700 mb-1">Notes</label>
        <textarea name="notes" rows="2"
            class="w-full border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">{{ old('notes', $measurement->notes ?? '') }}</textarea>
    </div>
</div>
