<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;

class Customer extends Authenticatable
{
    protected $fillable = [
        'name',
        'phone',
        'email',
        'address',
        'city',
        'pincode',
    ];

    public function cartItems()
    {
        return $this->hasMany(CartItem::class);
    }

    public function wishlistItem()
    {
        return $this->hasMany(WishlistItem::class);
    }
}
