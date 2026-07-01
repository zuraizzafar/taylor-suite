@extends('layouts.app')
@section('title', __('Fabric History'))
@section('page-title', __('Fabric History') . ': ' . $fabric->roll_number)

@section('content')
<div class="pt-2 space-y-6">

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 flex items-center justify-between">
        <div>
            <p class="text-lg font-bold text-slate-800">{{ $fabric->fabric_type }} — {{ $fabric->color }}</p>
            <p class="text-sm text-slate-500">{{ __('Roll') }}: {{ $fabric->roll_number }}</p>
        </div>
        <div class="text-right">
            <p class="text-xs text-slate-500">{{ __('Remaining') }}</p>
            <p class="text-lg font-bold {{ $fabric->status_badge }} inline-block px-2 rounded">{{ number_format($fabric->available_meter, 1) }}m</p>
        </div>
    </div>

    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Date') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Type') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Meter') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('Note') }}</th>
                    <th class="px-4 py-3 text-left font-medium">{{ __('By') }}</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($movements as $m)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 text-slate-600 text-xs">{{ $m->created_at->format('d M Y H:i') }}</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium
                            {{ match($m->type) {
                                'added' => 'bg-green-100 text-green-700',
                                'suit_used', 'fabric_sale' => 'bg-blue-100 text-blue-700',
                                'return' => 'bg-purple-100 text-purple-700',
                                'damage' => 'bg-red-100 text-red-700',
                                default => 'bg-slate-100 text-slate-700',
                            } }}">
                            {{ ucfirst(str_replace('_', ' ', $m->type)) }}
                        </span>
                    </td>
                    <td class="px-4 py-3 font-medium">{{ number_format($m->meter, 2) }}m</td>
                    <td class="px-4 py-3 text-slate-600">{{ $m->note ?? '—' }}</td>
                    <td class="px-4 py-3 text-slate-500 text-xs">{{ $m->user?->name ?? '—' }}</td>
                </tr>
                @empty
                <tr><td colspan="5" class="px-4 py-8 text-center text-slate-400">{{ __('No movements recorded.') }}</td></tr>
                @endforelse
            </tbody>
        </table>

        @if($movements->hasPages())
        <div class="px-4 py-3 border-t border-slate-100">{{ $movements->links() }}</div>
        @endif
    </div>

    <a href="{{ route('fabrics.index') }}" class="text-sm text-blue-600 hover:underline">← {{ __('Back to Fabric Stock') }}</a>
</div>
@endsection
