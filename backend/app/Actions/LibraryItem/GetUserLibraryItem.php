<?php

declare(strict_types=1);

namespace App\Actions\LibraryItem;

use App\Models\LibraryItem;

class GetUserLibraryItem
{
    public function handle(int $libItemId): LibraryItem
    {
        return LibraryItem::with(['item', 'user'])
            ->where('user_id', auth()->id())
            ->where('id', $libItemId)
            ->firstOrFail();
    }
}
