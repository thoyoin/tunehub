<?php

declare(strict_types=1);

namespace App\Actions\LibraryItem;

class GetItemTracks
{
    public function handle($libraryItem)
    {
        return $libraryItem
            ->item()
            ->with('tracks.release')
            ->get();
    }
}
