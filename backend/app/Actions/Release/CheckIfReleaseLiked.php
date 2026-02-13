<?php

declare(strict_types=1);

namespace App\Actions\Release;

class CheckIfReleaseLiked
{
    public function handle($release): bool
    {
        return auth()
            ->user()
            ->libraryItems()
            ->where('item_type', 'release')
            ->where('item_id', $release->id)
            ->exists();
    }
}
