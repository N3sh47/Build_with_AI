<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LaundryOrderItem extends Model
{
    use HasFactory;

    protected $fillable = [
        'laundry_order_id',
        'service_name',
        'garment_description',
        'quantity',
        'unit_price',
        'line_total',
    ];

    public function order(): BelongsTo
    {
        return $this->belongsTo(LaundryOrder::class, 'laundry_order_id');
    }
}
