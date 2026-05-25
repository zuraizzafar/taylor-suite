<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TranslationOverride extends Model
{
    protected $fillable = ['locale', 'key', 'value'];
}
