<?php

declare(strict_types=1);

namespace App\Actions\Playlist;

class GetUserLikedPlaylist
{
    public function handle()
    {
        return auth()
            ->user()
            ->libraryItems()
            ->where('item_type', 'playlist')
            ->with('item')
            ->first()
            ->item;
    }
}
