<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricMovement extends Model
{
    protected $fillable = [
        'fabric_id',
        'type',
        'meter',
        'reference_type',
        'reference_id',
        'note',
        'user_id',
    ];

    protected $casts = [
        'meter' => 'decimal:2',
    ];

    public function fabric(): BelongsTo
    {
        return $this->belongsTo(Fabric::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
