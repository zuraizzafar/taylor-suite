<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_id',
        'category',
        'amount',
        'description',
        'date',
    ];

    protected $casts = [
        'date' => 'date',
    ];

    public const CATEGORIES = [
        'Rent'             => 'Rent',
        'Electricity'      => 'Electricity',
        'Water'            => 'Water',
        'Salary Advance'   => 'Salary Advance',
        'Fabric Purchase'  => 'Fabric Purchase',
        'Equipment'        => 'Equipment',
        'Maintenance'      => 'Maintenance',
        'Miscellaneous'    => 'Miscellaneous',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }
}
