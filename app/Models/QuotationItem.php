<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class QuotationItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'quotation_id',
        'description',
        'qty',
        'rate',
        'sort_order',
    ];

    protected $casts = [
        'qty'  => 'decimal:2',
        'rate' => 'decimal:2',
    ];

    public function quotation(): BelongsTo
    {
        return $this->belongsTo(Quotation::class);
    }

    public function getLineTotalAttribute(): float
    {
        return round(((float) $this->qty) * ((float) $this->rate), 2);
    }
}
