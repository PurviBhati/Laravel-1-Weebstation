<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Invoice extends Model
{
    protected $fillable = [
        'invoice_number',
        'invoice_date',
        'customer_name',
        'customer_phone',
        'subtotal',
        'discount',
        'total',
        'payment_method',
        'notes',
        'created_by',
    ];

    protected $casts = ['invoice_date' => 'date'];

    public function items()
    {
        return $this->hasMany(InvoiceItem::class);
    }
}
