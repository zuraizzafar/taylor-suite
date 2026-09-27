<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderItem extends Model
{
    protected $fillable = [
        'order_id',
        'description',
        'qty',
        'rate',
        'sort_order',
    ];

    protected $casts = [
        'qty'  => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function getLineTotalAttribute(): float
    {
        return round(((float) $this->qty) * ((float) $this->rate), 2);
    }
}
