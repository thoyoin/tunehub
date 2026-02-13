<?php

declare(strict_types=1);

namespace App\Actions\LibraryItem;

use App\Models\LibraryItem;

class CreateLibraryItem
{
    public function handle($userId, $playlistId): LibraryItem
    {
        return LibraryItem::create([
            'user_id' => $userId,
            'item_type' => 'playlist',
            'item_id' => $playlistId,
        ]);
    }
}
