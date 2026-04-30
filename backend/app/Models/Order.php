<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

class Order extends Model
{
    protected $fillable = [
        'user_id',
        'status',
        'total_price',
        'customer_name',
        'customer_email',
        'customer_address',
        'stripe_payment_intent_id',
        'stripe_session_id',
    ];

    protected $hidden = [
        'stripe_session_id',
        'stripe_payment_intent_id'
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function products(): BelongsToMany
    {
        return $this->belongsToMany(Product::class, 'order_product')
            ->withPivot([
                'product_variant_id',
                'quantity',
                'unit_price',
                'subtotal',
                'title_snapshot',
            ])
            ->withTimestamps();
    }
}
