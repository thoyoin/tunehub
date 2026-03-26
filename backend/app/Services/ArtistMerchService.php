<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;

class ArtistMerchService
{
    public function get($slug)
    {
        return Product::where('slug', $slug)
            ->with(['productImages', 'productVariants'])
            ->first();
    }
}
