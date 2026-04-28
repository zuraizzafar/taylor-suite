<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerStitchRate extends Model
{
    protected $fillable = ['worker_id', 'stitch_type_id', 'price'];

    protected $casts = ['price' => 'decimal:2'];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function stitchType(): BelongsTo
    {
        return $this->belongsTo(StitchType::class);
    }
}
