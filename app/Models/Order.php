<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number',
        'customer_id',
        'customer_name',
        'customer_email',
        'customer_phone',
        'address', 'city', 'pincode',
        'subtotal', 'shipping_charge', 'gst_rate', 'gst_amount',
        'total',
        'payment_method', 'payment_status',
        'status', 'paid-_at', 'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongdTo(Customer::class);
    }
}
