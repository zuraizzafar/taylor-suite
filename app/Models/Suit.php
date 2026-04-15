<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Suit extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_id',
        'measurement_id',
        'worker_id',
        'branch_id',
        'suit_number',
        'suit_code',
        'suit_type',
        'fabric_meter',
        'fabric_description',
        'status',
        'notes',
        'qr_code_path',
        'delivered_at',
        'worker_earning',
        'stitching_started_at',
    ];

    protected $casts = [
        'delivered_at'         => 'datetime',
        'stitching_started_at' => 'datetime',
    ];

    public const STATUSES = ['pending', 'cutting', 'stitching', 'ready', 'delivered'];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function order(): BelongsTo
    {
        return $this->belongsTo(Order::class);
    }

    public function measurement(): BelongsTo
    {
        return $this->belongsTo(Measurement::class);
    }

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'pending'   => 'bg-gray-200 text-gray-800',
            'cutting'   => 'bg-yellow-200 text-yellow-800',
            'stitching' => 'bg-blue-200 text-blue-800',
            'ready'     => 'bg-green-200 text-green-800',
            'delivered' => 'bg-purple-200 text-purple-800',
            default     => 'bg-gray-200 text-gray-800',
        };
    }
}
