<?php

declare(strict_types=1);

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Product extends Model
{
    use HasFactory;

    protected $fillable = [
        'slug',
        'title',
        'description',
        'user_id',
        'status',
        'cover_url',
        'currency',
        'stripe_product_id',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function (Product $product) {
            $product->slug = Str::slug($product->title);
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function order(): BelongsToMany
    {
        return $this->belongsToMany(Order::class, 'order_product')
            ->withPivot([
                'product_variant_id',
                'quantity',
                'unit_price',
                'subtotal',
                'title_snapshot',
            ])
            ->withTimestamps();
    }

    public function productVariants(): HasMany
    {
        return $this->hasMany(ProductVariant::class);
    }

    public function productImages(): HasMany
    {
        return $this->hasMany(ProductImage::class);
    }
}
