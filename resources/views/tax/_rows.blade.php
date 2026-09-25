{{-- Subtotal / Discount / Tax rows for a totals <table>. Expects $doc (order, quotation or fabric sale) and $taxLabel. --}}
@php
    $discountAmount = (float) ($doc->discount_amount ?? 0);
    $taxMode        = $doc->tax_mode ?? 'none';
    $charged        = $taxMode === 'inclusive';
    $ratePct        = \App\Services\TaxService::rateLabel($doc->tax_rate ?? 0);
    $fmt            = fn ($v) => \App\Services\TaxService::fmt($v);
@endphp
@if($discountAmount > 0 || $taxMode !== 'none')
<tr>
    <td class="lbl">{{ __('Subtotal') }}</td>
    <td class="val" style="font-family:DejaVu Sans,sans-serif;font-weight:400">Rs {{ $fmt($doc->subtotal) }}</td>
</tr>
@if($discountAmount > 0)
<tr>
    <td class="lbl" style="color:#dc2626">{{ __('Discount') }}@if(($doc->discount_type ?? null) === 'percent') ({{ \App\Services\TaxService::rateLabel($doc->discount_value) }})@endif</td>
    <td class="val" style="font-family:DejaVu Sans,sans-serif;color:#dc2626">&minus; Rs {{ $fmt($discountAmount) }}</td>
</tr>
@endif
@if($taxMode !== 'none')
<tr>
    <td class="lbl" style="{{ $charged ? '' : 'color:#94a3b8' }}">{{ $taxLabel }} ({{ $ratePct }})@if(! $charged) &nbsp;<span style="font-size:8px">{{ __('not charged') }}</span>@endif</td>
    <td class="val" style="font-family:DejaVu Sans,sans-serif;{{ $charged ? '' : 'color:#94a3b8;font-weight:400' }}">@if($charged)+ @endif Rs {{ $fmt($doc->tax_amount) }}</td>
</tr>
@endif
@endif
