<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    protected $fillable = [
        'category',
        'description',
        'amount',
        'incurred_on',
        'recorded_by',
    ];

    protected $casts = [
        'incurred_on' => 'date',
    ];
}
