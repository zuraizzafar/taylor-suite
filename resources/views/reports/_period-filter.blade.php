{{-- Date-range filter bar with presets, PDF and Excel buttons. Params: $route, $pdfRoute, $csvRoute --}}
<div class="bg-white rounded-xl border border-slate-100 shadow-sm p-4">
    <form method="GET" action="{{ route($route) }}" class="flex flex-wrap items-end gap-3">
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('From:') }}</label>
            <input type="date" name="from" value="{{ $from }}"
                class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-500 mb-1">{{ __('To:') }}</label>
            <input type="date" name="to" value="{{ $to }}"
                class="border border-slate-300 rounded-lg px-3 py-1.5 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
        </div>
        <button class="bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-4 py-1.5 rounded-lg">{{ __('Filter') }}</button>
        <a href="{{ route($csvRoute, request()->all()) }}"
           class="bg-emerald-600 hover:bg-emerald-700 text-white text-sm px-4 py-1.5 rounded-lg font-semibold">📥 {{ __('Export Excel') }}</a>
        <a href="{{ route($pdfRoute, request()->all()) }}" target="_blank"
           class="bg-slate-700 hover:bg-slate-800 text-white text-sm px-4 py-1.5 rounded-lg font-semibold">📄 {{ __('Print PDF') }}</a>
        <div class="flex gap-1">
            @foreach(['Today' => 'today', 'This Week' => 'week', 'This Month' => 'month', 'Last Month' => 'last_month'] as $label => $val)
            <a href="{{ route($route, ['preset' => $val]) }}"
               class="px-3 py-1.5 rounded-lg text-xs font-medium border {{ ($preset ?? '') === $val ? 'bg-blue-600 text-white border-blue-600' : 'bg-slate-50 text-slate-600 border-slate-200 hover:bg-slate-100' }}">
                {{ __($label) }}
            </a>
            @endforeach
        </div>
    </form>
</div>
