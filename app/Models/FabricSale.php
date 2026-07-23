<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class FabricSale extends Model
{
    protected $fillable = [
        'fabric_id',
        'branch_id',
        'customer_name',
        'customer_mobile',
        'meter',
        'rate',
        'total_amount',
        'sale_code',
        'sold_by',
    ];

    protected $casts = [
        'meter'        => 'decimal:2',
        'rate'         => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function fabric(): BelongsTo
    {
        return $this->belongsTo(Fabric::class)->withTrashed();
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function soldBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'sold_by');
    }
}
