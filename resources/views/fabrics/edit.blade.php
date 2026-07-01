@extends('layouts.app')
@section('title', __('Edit Fabric'))
@section('page-title', __('Edit Fabric'))

@section('content')
<div class="max-w-xl pt-4">
    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-6">
        <p class="text-xs text-slate-500 mb-4">{{ __('Available') }}: {{ number_format($fabric->available_meter, 1) }}m / {{ __('Total') }}: {{ number_format($fabric->total_meter, 1) }}m</p>
        <form method="POST" action="{{ route('fabrics.update', $fabric) }}">
            @csrf
            @method('PUT')
            @include('fabrics._form', ['fabric' => $fabric])
            <div class="flex gap-3 mt-6">
                <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">
                    {{ __('Save') }}
                </button>
                <a href="{{ route('fabrics.index') }}"
                    class="bg-slate-100 hover:bg-slate-200 text-slate-700 font-medium px-5 py-2 rounded-lg text-sm">
                    {{ __('Cancel') }}
                </a>
            </div>
        </form>
    </div>
</div>
@endsection
