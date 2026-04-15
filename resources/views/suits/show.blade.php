@extends('layouts.app')
@section('title', $suit->suit_code)
@section('page-title', 'Suit: ' . $suit->suit_code)

@section('content')
<div class="pt-2 space-y-6">

    {{-- Suit header --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <div class="flex items-start justify-between">
            <div>
                <p class="text-xs text-slate-500">Customer</p>
                <a href="{{ route('customers.show', $suit->customer) }}"
                   class="text-lg font-bold text-slate-800 hover:text-blue-600">{{ $suit->customer->name }}</a>
                <p class="text-sm text-slate-500">{{ $suit->customer->file_number }} · {{ $suit->customer->mobile }}</p>
            </div>
            <div class="text-right">
                <span class="font-mono text-blue-700 font-bold text-xl">{{ $suit->suit_code }}</span>
                <div class="flex gap-2 mt-2 justify-end">
                    <a href="{{ route('suits.edit', $suit) }}"
                       class="text-xs bg-slate-100 hover:bg-slate-200 px-3 py-1 rounded-lg">Edit</a>
                    <a href="{{ route('suits.tag', $suit) }}" target="_blank"
                       class="text-xs bg-yellow-500 hover:bg-yellow-600 text-white px-3 py-1 rounded-lg">Print Tag</a>
                    {{-- Copy public link button --}}
                    <button
                        id="copy-link-btn"
                        data-url="{{ route('scan.show', $suit->suit_code) }}"
                        onclick="copyPublicLink(this)"
                        class="text-xs bg-slate-700 hover:bg-slate-800 text-white px-3 py-1 rounded-lg transition">
                        Copy Public Link
                    </button>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 md:grid-cols-4 gap-4 mt-5 pt-5 border-t border-slate-100">
            <div>
                <p class="text-xs text-slate-500">Suit Type</p>
                <p class="text-sm font-semibold text-slate-700">{{ $suit->suit_type }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Fabric</p>
                <p class="text-sm font-semibold text-slate-700">{{ $suit->fabric_meter }}m {{ $suit->fabric_description }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Worker</p>
                <p class="text-sm font-semibold text-slate-700">{{ $suit->worker?->name ?? '—' }}</p>
            </div>
            <div>
                <p class="text-xs text-slate-500">Order</p>
                @if($suit->order)
                <a href="{{ route('orders.show', $suit->order) }}"
                   class="text-sm font-semibold text-blue-600 hover:underline">{{ $suit->order->order_number }}</a>
                @else
                <p class="text-sm text-slate-400">—</p>
                @endif
            </div>
        </div>

        @if($suit->notes)
        <p class="mt-3 text-sm text-slate-500">📝 {{ $suit->notes }}</p>
        @endif
    </div>

    {{-- Status control --}}
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-700 mb-4">📋 Status</h3>

        <div class="flex items-center gap-4 mb-5">
            @php $statuses = \App\Models\Suit::STATUSES; @endphp
            @foreach($statuses as $i => $s)
            <div class="flex items-center gap-1">
                <span class="text-xs px-2 py-1 rounded-full font-medium
                    {{ $suit->status === $s ? 'bg-blue-600 text-white' : (array_search($suit->status, $statuses) > $i ? 'bg-green-100 text-green-700' : 'bg-slate-100 text-slate-500') }}">
                    {{ ucfirst($s) }}
                </span>
                @if(!$loop->last)<span class="text-slate-300">→</span>@endif
            </div>
            @endforeach
        </div>

        @if($suit->status !== 'delivered')
        <form method="POST" action="{{ route('suits.status', $suit) }}" class="flex items-center gap-3">
            @csrf @method('PATCH')
            <select name="status"
                class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                @foreach($statuses as $s)
                <option value="{{ $s }}" {{ $suit->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Update Status
            </button>
        </form>
        @else
        <div class="p-3 bg-purple-50 border border-purple-200 rounded-lg text-sm text-purple-700">
            ✅ Delivered on {{ $suit->delivered_at?->format('d M Y, h:i A') }}
        </div>
        @endif
    </div>

    {{-- Assign Worker --}}
    @if(auth()->user()->isAdmin())
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-700 mb-3">👨‍🔧 Assign Worker</h3>
        <form method="POST" action="{{ route('suits.assign-worker', $suit) }}" class="flex items-center gap-3">
            @csrf @method('PATCH')
            <select name="worker_id"
                class="border border-slate-300 rounded-lg px-3 py-2 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500">
                <option value="">— unassign —</option>
                @foreach($workers as $w)
                <option value="{{ $w->id }}" {{ $suit->worker_id == $w->id ? 'selected' : '' }}>{{ $w->name }}</option>
                @endforeach
            </select>
            <button type="submit" class="bg-slate-700 hover:bg-slate-800 text-white text-sm font-medium px-4 py-2 rounded-lg">
                Assign
            </button>
        </form>
    </div>
    @endif

    {{-- QR Code display --}}
    @if($suit->qr_code_path && Storage::disk('public')->exists($suit->qr_code_path))
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-700 mb-3">QR Code</h3>
        <div class="flex items-start gap-6">
            <img src="{{ Storage::url($suit->qr_code_path) }}" alt="QR Code" class="w-32 h-32">
            <div class="flex-1">
                <p class="text-sm text-slate-600 mb-2">Scan to open the public status page.</p>
                <div class="flex items-center gap-2">
                    <code id="public-url-display" class="text-xs font-mono bg-slate-100 px-2 py-1.5 rounded flex-1 break-all">{{ route('scan.show', $suit->suit_code) }}</code>
                    <button onclick="copyPublicLink(document.getElementById('copy-link-btn'))"
                        class="text-xs bg-slate-700 hover:bg-slate-800 text-white px-3 py-1.5 rounded-lg whitespace-nowrap">Copy</button>
                </div>
                <a href="{{ route('scan.show', $suit->suit_code) }}" target="_blank"
                    class="mt-2 inline-block text-xs text-blue-600 hover:underline">Open in new tab</a>
            </div>
        </div>
    </div>
    @endif

    {{-- Measurements --}}
    @if($suit->measurement)    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
        <h3 class="font-semibold text-slate-700 mb-3">📏 Measurements – {{ $suit->measurement->label }}</h3>
        <div class="grid grid-cols-2 gap-4">
            <div>
                <h4 class="text-xs font-semibold text-slate-500 uppercase mb-2">Qameez</h4>
                <div class="grid grid-cols-2 gap-1 text-sm">
                    @foreach(['q_length' => 'Length', 'q_shoulder' => 'Shoulder', 'q_chest' => 'Chest', 'q_waist' => 'Waist', 'q_seat' => 'Seat', 'q_sleeve' => 'Sleeve', 'q_sleeve_width' => 'Sleeve W.', 'q_collar' => 'Collar', 'q_front' => 'Front', 'q_back' => 'Back', 'q_armhole' => 'Armhole', 'q_cuff' => 'Cuff'] as $f => $l)
                    @if($suit->measurement->$f)
                    <div class="flex justify-between border-b border-slate-50 py-0.5">
                        <span class="text-slate-500">{{ $l }}</span>
                        <span class="font-medium">{{ $suit->measurement->$f }}"</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
            <div>
                <h4 class="text-xs font-semibold text-slate-500 uppercase mb-2">Shalwar</h4>
                <div class="grid grid-cols-2 gap-1 text-sm">
                    @foreach(['s_length' => 'Length', 's_waist' => 'Waist', 's_seat' => 'Seat', 's_thigh' => 'Thigh', 's_knee' => 'Knee', 's_bottom' => 'Bottom', 's_crotch' => 'Crotch', 's_ankle' => 'Ankle'] as $f => $l)
                    @if($suit->measurement->$f)
                    <div class="flex justify-between border-b border-slate-50 py-0.5">
                        <span class="text-slate-500">{{ $l }}</span>
                        <span class="font-medium">{{ $suit->measurement->$f }}"</span>
                    </div>
                    @endif
                    @endforeach
                </div>
            </div>
        </div>
    </div>
    @endif

</div>
@endsection

@push('scripts')
<script>
function copyPublicLink(btn) {
    const url = btn.dataset.url || '{{ route('scan.show', $suit->suit_code) }}';
    navigator.clipboard.writeText(url).then(function () {
        const original = btn.textContent;
        btn.textContent = 'Copied!';
        btn.classList.remove('bg-slate-700', 'hover:bg-slate-800');
        btn.classList.add('bg-green-600');
        setTimeout(function () {
            btn.textContent = original;
            btn.classList.add('bg-slate-700', 'hover:bg-slate-800');
            btn.classList.remove('bg-green-600');
        }, 2000);
    }).catch(function () {
        // fallback for older browsers
        const ta = document.createElement('textarea');
        ta.value = url;
        document.body.appendChild(ta);
        ta.select();
        document.execCommand('copy');
        document.body.removeChild(ta);
        btn.textContent = 'Copied!';
        setTimeout(function () { btn.textContent = 'Copy Public Link'; }, 2000);
    });
}
</script>
@endpush
