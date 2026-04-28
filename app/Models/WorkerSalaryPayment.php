<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class WorkerSalaryPayment extends Model
{
    protected $fillable = [
        'worker_id', 'branch_id',
        'period_from', 'period_to',
        'total_suits', 'total_earned', 'amount_paid',
        'notes', 'paid_by', 'paid_at',
    ];

    protected $casts = [
        'period_from'  => 'date',
        'period_to'    => 'date',
        'total_earned' => 'decimal:2',
        'amount_paid'  => 'decimal:2',
        'paid_at'      => 'datetime',
    ];

    public function worker(): BelongsTo
    {
        return $this->belongsTo(Worker::class);
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function paidBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'paid_by');
    }
}
