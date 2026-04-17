<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CheckoutOrder extends Model
{
    protected $fillable = [
        'public_order_id',
        'customer_id',
        'customer_email',
        'box_slug',
        'box_title',
        'restaurant_name',
        'amount_idr',
        'payment_method',
        'fulfillment_method',
        'delivery_address',
        'midtrans_transaction_id',
        'payment_status',
        'payment_redirect_url',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }
}
