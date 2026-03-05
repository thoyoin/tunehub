<?php

declare(strict_types=1);

namespace App\Actions\Playlist;

use App\Models\LibraryItem;

class GetPlaylistById
{
    public function handle($playlist): LibraryItem
    {
//        return $playlist
//            ->with(['user', 'libraryItem'])
//            ->where('id', $playlist->id)
//            ->first();
        return LibraryItem::where('item_id', $playlist->id)
            ->with('user')
            ->first();
    }
}
