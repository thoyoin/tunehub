<?php

declare(strict_types=1);

namespace App\Actions\Playlist;

class GetOrderedPlaylistTracks
{
    public function handle($playlist)
    {
        return $playlist
            ->tracks()
            ->orderBy('pivot_position')
            ->with('release')
            ->get();
    }
}
