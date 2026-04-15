<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Order extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'branch_id',
        'order_number',
        'order_date',
        'delivery_date',
        'total_amount',
        'advance_amount',
        'balance_amount',
        'notes',
    ];

    protected $casts = [
        'order_date'    => 'date',
        'delivery_date' => 'date',
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
     * Generate the next order number (ORD-YYYY-NNN).
     */
    public static function nextOrderNumber(): string
    {
        $year = date('Y');
        $count = DB::table('orders')
            ->whereYear('created_at', $year)
            ->count();
        return 'ORD-' . $year . '-' . str_pad($count + 1, 3, '0', STR_PAD_LEFT);
    }
}
