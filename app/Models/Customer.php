<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $hidden = [
        'password',
    ];

    public function checkoutOrders(): HasMany
    {
        return $this->hasMany(CheckoutOrder::class);
    }

    public function cart()
    {
        return $this->hasOne(Cart::class);
    }
}
