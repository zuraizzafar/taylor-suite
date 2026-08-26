<?php

namespace App\Models;

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
        'total_amount',
        'advance_percentage',
        'advance_amount',
        'balance_amount',
        'design_reference',
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
        $total = $this->items()->get()->sum(fn (QuotationItem $item) => $item->qty * $item->rate);
        $advance = round($total * ($this->advance_percentage / 100), 2);

        $this->total_amount   = $total;
        $this->advance_amount = $advance;
        $this->balance_amount = max(0, $total - $advance);
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
