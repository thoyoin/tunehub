<?php

declare(strict_types=1);

namespace App\Actions\AdminPanel\merch;

use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class GetAllMerch
{
    public function handle(Request $request): LengthAwarePaginator
    {
        $status = $request->query('status');

        return Product::query()
            ->with(['productVariants', 'productImages', 'user'])
            ->when($status && $status !== 'all', fn ($q) => $q->where('status', $status))
            ->paginate(10);
    }
}
