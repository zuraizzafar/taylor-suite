<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExtraType extends Model
{
    protected $fillable = ['name', 'default_price', 'is_active'];

    protected $casts = [
        'default_price' => 'decimal:2',
        'is_active'     => 'boolean',
    ];
}
