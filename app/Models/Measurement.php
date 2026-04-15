<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Measurement extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id', 'label',
        'q_length', 'q_shoulder', 'q_chest', 'q_waist', 'q_seat',
        'q_sleeve', 'q_sleeve_width', 'q_collar', 'q_front', 'q_back',
        'q_armhole', 'q_cuff',
        's_length', 's_waist', 's_seat', 's_thigh', 's_knee',
        's_bottom', 's_crotch', 's_ankle',
        'notes', 'meta',
    ];

    protected $casts = [
        'meta' => 'array',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function suits(): HasMany
    {
        return $this->hasMany(Suit::class);
    }
}
