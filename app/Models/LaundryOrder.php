<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LaundryOrder extends Model
{
    use HasFactory;

    protected $fillable = [
        'customer_id',
        'order_number',
        'drop_off_date',
        'expected_pickup_date',
        'status',
        'total_amount',
        'paid_amount',
        'special_instructions',
    ];

    protected $casts = [
        'drop_off_date' => 'date',
        'expected_pickup_date' => 'date',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(LaundryOrderItem::class);
    }

    public function statuses(): HasMany
    {
        return $this->hasMany(LaundryOrderStatus::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }
}
