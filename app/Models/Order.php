<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;
use App\Traits\HasUniqueNumber;

class Order extends Model
{
    use HasFactory, HasUniqueNumber;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'order_number',
        'order_date',
        'delivery_date',
        'subtotal',
        'discount_type',
        'discount_value',
        'discount_amount',
        'tax_mode',
        'tax_rate',
        'tax_amount',
        'total_amount',
        'advance_amount',
        'balance_amount',
        'notes',
        'extras',
    ];

    protected $casts = [
        'order_date'    => 'date',
        'delivery_date' => 'date',
        'extras'        => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function suits(): HasMany
    {
        return $this->hasMany(Suit::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class)->orderBy('sort_order');
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    /**
     * Recalculate advance_amount and balance_amount from all payments + original advance.
     * Call this after adding/removing a payment.
     */
    public function recalculateBalance(): void
    {
        $paid = $this->payments()->sum('amount');
        $this->advance_amount = $paid;
        $this->balance_amount = max(0, $this->total_amount - $paid);
        $this->saveQuietly();
    }

    /**
     * Generate the next order number (ORD-YYYY-NNN). $offset shifts past a known collision
     * (see HasUniqueNumber::createWithUniqueNumber()) so a retry doesn't recompute the same value.
     */
    public static function nextOrderNumber(int $offset = 0): string
    {
        $year = date('Y');
        $count = DB::table('orders')
            ->whereYear('created_at', $year)
            ->count();
        return 'ORD-' . $year . '-' . str_pad($count + 1 + $offset, 3, '0', STR_PAD_LEFT);
    }
}
