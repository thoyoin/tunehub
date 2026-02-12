<?php

declare(strict_types=1);

namespace App\Actions\LibraryItem;

use App\Models\LibraryItem;

class CreateLibraryItem
{
    public function handle($user, $playlist): void
    {
        LibraryItem::create([
            'user_id' => $user->id,
            'item_type' => 'playlist',
            'item_id' => $playlist->id,
        ]);
    }
}
