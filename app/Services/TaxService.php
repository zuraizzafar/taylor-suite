<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Setting;
use Illuminate\Http\Request;

class TaxService
{
    public const MODE_NONE      = 'none';
    public const MODE_EXCLUSIVE = 'exclusive'; // tax shown on the document, NOT added to what the client pays
    public const MODE_INCLUSIVE = 'inclusive'; // tax shown and added on top: the client pays it

    public const MODES = [self::MODE_NONE, self::MODE_EXCLUSIVE, self::MODE_INCLUSIVE];
    public const MODULES = ['quotation', 'order', 'fabric_sale'];

    public static function modeLabels(): array
    {
        return [
            self::MODE_NONE      => __('No tax'),
            self::MODE_EXCLUSIVE => __('Exclusive — tax shown, not charged to client'),
            self::MODE_INCLUSIVE => __('Inclusive — tax shown and charged to client'),
        ];
    }

    /**
     * Effective tax profile for a module + branch.
     * Branch overrides (mode, rate, registration no) win over the shop-wide settings;
     * the per-module on/off toggle is always shop-wide.
     *
     * @return array{enabled: bool, mode: string, rate: float, label: string, registration_no: string}
     */
    public static function resolve(string $module, ?int $branchId = null): array
    {
        $s = Setting::allKeyed();
        $branch = $branchId ? Branch::find($branchId) : null;

        $enabled = ($s["tax_{$module}_enabled"] ?? '1') !== '0';

        $mode = $s['tax_default_mode'] ?? self::MODE_NONE;
        $rateOverride = $s["tax_rate_{$module}"] ?? '';
        $rate = $rateOverride !== '' ? (float) $rateOverride : (float) ($s['tax_default_rate'] ?? 0);
        $registration = (string) ($s['tax_registration_no'] ?? '');

        if ($branch) {
            if ($branch->tax_mode) {
                $mode = $branch->tax_mode;
            }
            if ($branch->tax_rate !== null) {
                $rate = (float) $branch->tax_rate;
            }
            if ($branch->tax_registration_no) {
                $registration = $branch->tax_registration_no;
            }
        }

        if (! in_array($mode, self::MODES, true)) {
            $mode = self::MODE_NONE;
        }

        return [
            'enabled'         => $enabled,
            'mode'            => $enabled ? $mode : self::MODE_NONE,
            'rate'            => $rate,
            'label'           => ($s['tax_label'] ?? '') !== '' ? $s['tax_label'] : 'GST',
            'registration_no' => $registration,
        ];
    }

    /** Resolved defaults for every branch (key "" = no branch), for form JS. */
    public static function branchDefaults(string $module): array
    {
        $out = ['' => self::resolve($module, null)];
        foreach (Branch::pluck('id') as $id) {
            $out[(string) $id] = self::resolve($module, (int) $id);
        }
        return $out;
    }

    /**
     * Initial state for a form's withTax() mixin: stored values when editing a document,
     * otherwise the resolved shop/branch defaults. old() input wins after a validation error.
     */
    public static function formInit(string $module, ?int $branchId, $doc = null): array
    {
        $p = self::resolve($module, $branchId);

        return [
            'enabled'       => $p['enabled'],
            'label'         => $p['label'],
            'branchId'      => $branchId ? (string) $branchId : '',
            'taxMode'       => old('tax_mode', $doc?->tax_mode ?? $p['mode']),
            'taxRate'       => (float) old('tax_rate', $doc ? $doc->tax_rate : $p['rate']),
            'discountType'  => old('discount_type', $doc?->discount_type ?? ''),
            'discountValue' => (float) old('discount_value', $doc?->discount_value ?? 0),
            'defaults'      => self::branchDefaults($module),
        ];
    }

    /**
     * Discount is taken off the subtotal first, then tax is computed on what is left.
     * Discount and tax are rounded to whole rupees.
     *
     * @return array{subtotal: float, discount_type: ?string, discount_value: float, discount_amount: float,
     *               taxable: float, tax_mode: string, tax_rate: float, tax_amount: float,
     *               total_amount: float, tax_collected: bool}
     */
    public static function calculate(
        float $subtotal,
        ?string $discountType = null,
        float $discountValue = 0,
        string $mode = self::MODE_NONE,
        float $rate = 0
    ): array {
        $subtotal = max(0, $subtotal);

        if ($discountType === 'percent') {
            $discountValue  = min(100, max(0, $discountValue));
            $discountAmount = round($subtotal * $discountValue / 100);
        } elseif ($discountType === 'fixed') {
            $discountValue  = max(0, $discountValue);
            $discountAmount = min($subtotal, round($discountValue));
        } else {
            $discountType   = null;
            $discountValue  = 0;
            $discountAmount = 0;
        }

        $taxable = $subtotal - $discountAmount;

        if (! in_array($mode, self::MODES, true)) {
            $mode = self::MODE_NONE;
        }
        $rate = max(0, min(100, $rate));

        $taxAmount = $mode === self::MODE_NONE ? 0 : round($taxable * $rate / 100);
        $total = $mode === self::MODE_INCLUSIVE ? $taxable + $taxAmount : $taxable;

        return [
            'subtotal'        => $subtotal,
            'discount_type'   => $discountType,
            'discount_value'  => $discountValue,
            'discount_amount' => (float) $discountAmount,
            'taxable'         => (float) $taxable,
            'tax_mode'        => $mode,
            'tax_rate'        => $mode === self::MODE_NONE ? 0.0 : (float) $rate,
            'tax_amount'      => (float) $taxAmount,
            'total_amount'    => (float) $total,
            'tax_collected'   => $mode === self::MODE_INCLUSIVE,
        ];
    }

    /**
     * Compute the totals for a document from a request, falling back to the resolved
     * shop/branch defaults when the request carries no tax fields. Never trusts a
     * client-computed total.
     */
    public static function fromRequest(Request $request, string $module, float $subtotal, ?int $branchId, bool $allowDiscount = true): array
    {
        $profile = self::resolve($module, $branchId);

        if (! $profile['enabled']) {
            $mode = self::MODE_NONE;
            $rate = 0.0;
        } else {
            $mode = $request->input('tax_mode', $profile['mode']);
            $rate = $request->filled('tax_rate') ? (float) $request->input('tax_rate') : $profile['rate'];
        }

        $discountType  = $allowDiscount ? ($request->input('discount_type') ?: null) : null;
        $discountValue = $allowDiscount ? (float) $request->input('discount_value', 0) : 0;

        return self::calculate($subtotal, $discountType, $discountValue, (string) $mode, (float) $rate);
    }

    /** Columns of a calculate() result that get stored on orders/quotations. */
    public static function documentColumns(array $calc): array
    {
        return [
            'subtotal'        => $calc['subtotal'],
            'discount_type'   => $calc['discount_type'],
            'discount_value'  => $calc['discount_value'],
            'discount_amount' => $calc['discount_amount'],
            'tax_mode'        => $calc['tax_mode'],
            'tax_rate'        => $calc['tax_rate'],
            'tax_amount'      => $calc['tax_amount'],
            'total_amount'    => $calc['total_amount'],
        ];
    }

    /** 1,234 for whole numbers, 1,234.50 otherwise. */
    public static function fmt(float|int|string|null $value): string
    {
        $value = (float) $value;
        return number_format($value, fmod($value, 1) == 0.0 ? 0 : 2);
    }

    public static function rateLabel(float|string $rate): string
    {
        return rtrim(rtrim(number_format((float) $rate, 2), '0'), '.') . '%';
    }
}
