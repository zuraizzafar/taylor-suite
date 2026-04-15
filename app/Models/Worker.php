<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Worker extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mobile',
        'is_active',
        'rate_per_suit',
        'user_id',
        'branch_id',
    ];

    protected $casts = [
        'is_active'      => 'boolean',
        'rate_per_suit'  => 'decimal:2',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function suits(): HasMany
    {
        return $this->hasMany(Suit::class);
    }
}
