<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\DB;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'file_sequence',
        'file_number',
        'name',
        'mobile',
        'address',
        'notes',
    ];

    public function measurements(): HasMany
    {
        return $this->hasMany(Measurement::class);
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }

    public function suits(): HasMany
    {
        return $this->hasMany(Suit::class);
    }

    /**
     * Generate next file_number and file_sequence atomically.
     * Returns ['file_sequence' => int, 'file_number' => string]
     */
    public static function nextFileNumber(): array
    {
        $max = (int) DB::table('customers')->max('file_sequence');
        $next = $max + 1;
        return [
            'file_sequence' => $next,
            'file_number'   => 'ST-' . str_pad($next, 3, '0', STR_PAD_LEFT),
        ];
    }
}
