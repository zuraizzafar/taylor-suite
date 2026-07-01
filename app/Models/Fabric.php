<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class Fabric extends Model
{
    use SoftDeletes;

    protected $fillable = [
        'branch_id',
        'fabric_type',
        'brand',
        'color',
        'design_code',
        'roll_number',
        'total_meter',
        'available_meter',
        'cost_price',
        'sale_price',
        'supplier',
        'purchase_date',
        'qr_code_path',
        'status',
    ];

    protected $casts = [
        'total_meter'     => 'decimal:2',
        'available_meter' => 'decimal:2',
        'cost_price'      => 'decimal:2',
        'sale_price'      => 'decimal:2',
        'purchase_date'   => 'date',
    ];

    public const LOW_STOCK_THRESHOLD = 2;

    protected static function booted(): void
    {
        static::saving(function (Fabric $fabric) {
            $fabric->status = $fabric->available_meter <= 0
                ? 'out_of_stock'
                : ($fabric->available_meter < self::LOW_STOCK_THRESHOLD ? 'low_stock' : 'in_stock');
        });
    }

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function suits(): HasMany
    {
        return $this->hasMany(Suit::class);
    }

    public function movements(): HasMany
    {
        return $this->hasMany(FabricMovement::class);
    }

    public function sales(): HasMany
    {
        return $this->hasMany(FabricSale::class);
    }

    public function getStatusBadgeAttribute(): string
    {
        return match ($this->status) {
            'out_of_stock' => 'bg-red-200 text-red-800',
            'low_stock'    => 'bg-yellow-200 text-yellow-800',
            default        => 'bg-green-200 text-green-800',
        };
    }

    /**
     * Deduct meter from stock. Throws if insufficient — never allows negative stock.
     */
    public function deduct(float $meter, string $refType, ?int $refId = null, ?string $note = null): void
    {
        DB::transaction(function () use ($meter, $refType, $refId, $note) {
            $fabric = static::whereKey($this->id)->lockForUpdate()->first();

            if ($meter > (float) $fabric->available_meter) {
                throw ValidationException::withMessages([
                    'fabric_meter' => "Only {$fabric->available_meter}m available on roll {$fabric->roll_number}.",
                ]);
            }

            $fabric->available_meter = (float) $fabric->available_meter - $meter;
            $fabric->save();

            $fabric->movements()->create([
                'type'           => $refType,
                'meter'          => $meter,
                'reference_type' => $refType,
                'reference_id'   => $refId,
                'note'           => $note,
                'user_id'        => auth()->id(),
            ]);

            $this->available_meter = $fabric->available_meter;
            $this->status = $fabric->status;
        });
    }

    /**
     * Restore previously deducted meter back to stock.
     */
    public function restore(float $meter, string $refType, ?int $refId = null, ?string $note = null): void
    {
        DB::transaction(function () use ($meter, $refType, $refId, $note) {
            $fabric = static::whereKey($this->id)->lockForUpdate()->first();

            $fabric->available_meter = (float) $fabric->available_meter + $meter;
            $fabric->save();

            $fabric->movements()->create([
                'type'           => 'return',
                'meter'          => $meter,
                'reference_type' => $refType,
                'reference_id'   => $refId,
                'note'           => $note,
                'user_id'        => auth()->id(),
            ]);

            $this->available_meter = $fabric->available_meter;
            $this->status = $fabric->status;
        });
    }
}
