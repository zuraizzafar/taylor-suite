@extends('layouts.app')
@section('title', 'My Suits')
@section('page-title', 'My Suits – ' . $worker->name)

@section('content')
<div class="pt-2">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 overflow-hidden">
        <div class="px-5 py-4 border-b border-slate-100">
            <h3 class="font-semibold text-slate-700">Suits assigned to me</h3>
        </div>
        <table class="w-full text-sm">
            <thead class="bg-slate-50 text-slate-600">
                <tr>
                    <th class="px-4 py-3 text-left font-medium">Code</th>
                    <th class="px-4 py-3 text-left font-medium">Customer</th>
                    <th class="px-4 py-3 text-left font-medium">Type</th>
                    <th class="px-4 py-3 text-left font-medium">Fabric</th>
                    <th class="px-4 py-3 text-left font-medium">Status</th>
                    <th class="px-4 py-3 text-left font-medium">Update Status</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-50">
                @forelse($suits as $suit)
                <tr class="hover:bg-slate-50">
                    <td class="px-4 py-3 font-mono font-semibold text-blue-700">{{ $suit->suit_code }}</td>
                    <td class="px-4 py-3">
                        <p class="font-medium text-slate-800">{{ $suit->customer->name }}</p>
                        <p class="text-xs text-slate-500">{{ $suit->customer->mobile }}</p>
                    </td>
                    <td class="px-4 py-3 text-slate-700">{{ $suit->suit_type }}</td>
                    <td class="px-4 py-3 text-slate-600">{{ $suit->fabric_meter }}m</td>
                    <td class="px-4 py-3">
                        <span class="text-xs px-2 py-0.5 rounded-full font-medium {{ $suit->status_badge }}">
                            {{ ucfirst($suit->status) }}
                        </span>
                    </td>
                    <td class="px-4 py-3">
                        @if($suit->status !== 'delivered')
                        <form method="POST" action="{{ route('suits.status', $suit) }}" class="flex items-center gap-2">
                            @csrf @method('PATCH')
                            <select name="status"
                                class="text-xs border border-slate-300 rounded px-2 py-1 focus:outline-none focus:ring-1 focus:ring-blue-500">
                                @foreach(\App\Models\Suit::STATUSES as $s)
                                <option value="{{ $s }}" {{ $suit->status === $s ? 'selected' : '' }}>{{ ucfirst($s) }}</option>
                                @endforeach
                            </select>
                            <button type="submit"
                                class="text-xs bg-blue-600 hover:bg-blue-700 text-white px-2 py-1 rounded">Update</button>
                        </form>
                        @else
                        <span class="text-xs text-slate-400">Delivered</span>
                        @endif
                    </td>
                </tr>
                @empty
                <tr><td colspan="6" class="px-4 py-8 text-center text-slate-400">No suits assigned yet.</td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
