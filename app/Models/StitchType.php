<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StitchType extends Model
{
    protected $fillable = ['name', 'base_price', 'is_active', 'branch_id'];

    protected $casts = [
        'base_price' => 'decimal:2',
        'is_active'  => 'boolean',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function workerRates(): HasMany
    {
        return $this->hasMany(WorkerStitchRate::class);
    }

    public function suits(): HasMany
    {
        return $this->hasMany(Suit::class);
    }

    /**
     * Return the effective stitching price for a given worker.
     * Worker override wins; falls back to base_price.
     */
    public function priceForWorker(?Worker $worker): float
    {
        if ($worker) {
            $override = $this->workerRates()->where('worker_id', $worker->id)->first();
            if ($override) {
                return (float) $override->price;
            }
        }
        return (float) $this->base_price;
    }
}
