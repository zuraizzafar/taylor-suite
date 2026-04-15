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

    public function suits(): HasMany
    {
        return $this->hasMany(Suit::class);
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
