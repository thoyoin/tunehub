<?php

declare(strict_types=1);

namespace App\Actions\LibraryItem;

use App\Models\LibraryItem;

class CreateLibraryItem
{
    public function handle($userId, $itemId, $type): LibraryItem
    {
        return LibraryItem::create([
            'user_id' => $userId,
            'item_type' => $type,
            'item_id' => $itemId,
        ]);
    }
}
