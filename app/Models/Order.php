<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Order extends Model
{
    protected $fillable = [
        'order_number', 'checkout_token', 'customer_name', 'customer_phone', 'customer_email',
        'address', 'area', 'city', 'delivery_zone', 'notes', 'currency',
        'subtotal', 'delivery_charge', 'discount_amount', 'total',
        'payment_method', 'payment_status', 'status',
    ];

    protected $attributes = [
        'currency' => 'BDT', 'payment_method' => 'cod',
        'payment_status' => 'pending', 'status' => 'pending', 'discount_amount' => 0,
    ];

    protected $casts = [
        'subtotal' => 'decimal:2', 'delivery_charge' => 'decimal:2',
        'discount_amount' => 'decimal:2', 'total' => 'decimal:2',
    ];

    public function items(): HasMany
    {
        return $this->hasMany(OrderItem::class);
    }
}
