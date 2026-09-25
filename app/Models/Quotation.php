<?php

namespace App\Models;

use App\Services\TaxService;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Quotation extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'quotation_number',
        'quotation_date',
        'validity_days',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_mode',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'advance_percentage',
        'advance_amount',
        'balance_amount',
        'design_reference',
        'delivery_note',
        'sample_image_1',
        'sample_image_2',
        'notes',
        'status',
        'converted_order_id',
    ];

    protected $casts = [
        'quotation_date' => 'date',
    ];

    public const STATUSES = ['draft', 'converted'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(QuotationItem::class)->orderBy('sort_order');
    }

    public function convertedOrder(): BelongsTo
    {
        return $this->belongsTo(Order::class, 'converted_order_id');
    }

    public function getValidUntilAttribute(): \Illuminate\Support\Carbon
    {
        return $this->quotation_date->copy()->addDays($this->validity_days);
    }

    public function getIsExpiredAttribute(): bool
    {
        return $this->status === 'draft' && $this->valid_until->isPast();
    }

    /**
     * Recalculate total/advance/balance from line items.
     */
    public function recalculateTotals(): void
    {
        $subtotal = (float) $this->items()->get()->sum(fn (QuotationItem $item) => $item->qty * $item->rate);

        $calc = TaxService::calculate(
            $subtotal,
            $this->discount_type,
            (float) $this->discount_value,
            $this->tax_mode ?: TaxService::MODE_NONE,
            (float) $this->tax_rate
        );
        $advance = round($calc['total_amount'] * ($this->advance_percentage / 100), 2);

        $this->fill(TaxService::documentColumns($calc));
        $this->advance_amount = $advance;
        $this->balance_amount = max(0, $calc['total_amount'] - $advance);
        $this->save();
    }

    /**
     * Generate the next quotation number (QT-YYYY-NNN).
     */
    public static function nextQuotationNumber(): string
    {
        $year = date('Y');
        $count = DB::table('quotations')
            ->whereYear('created_at', $year)
            ->count();
        return 'QT-' . $year . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }
}
