<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Attributes\Scope;
use Illuminate\Database\Eloquent\Builder;
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
        'status', 'paid_at',
        'cancelled_at', 'cancelled_by',
        'address_updated_at', 'address_seen_at', 'previous_address',
        'notes',
    ];

    protected $casts = [
        'paid_at' => 'datetime',
        'cancelled_at' => 'datetime',
        'address_updated_at' => 'datetime',
        'address_seen_at' => 'datetime',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function addressLine(): string
    {
        return trim(
            $this->customer_name.','.$this->customer_phone.','
            .$this->address.','.$this->city.'-'.$this->pincode);
    }

    public function hasUnseenAddressChange(): bool
    {
        return $this->address_updated_at !== null
            && ($this->address_seen_at === null
                || $this->address_seen_at->lt($this->address_updated_at));
    }

    #[Scope]
    protected function unseenAddressChange(Builder $query): void
    {
        $query->whereNotNull('address_updated_at')
            ->where(function (Builder $query): void {
                $query->whereNull('address_seen_at')
                    ->orWhereColumn('address_seen_at', '<', 'address_updated_at');
            });
    }
}
