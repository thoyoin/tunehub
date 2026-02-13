<?php

declare(strict_types=1);

namespace App\Actions\Playlist;

use App\Models\Playlist;

class GetPlaylistById
{
    public function handle($playlist): PLaylist
    {
        return $playlist
            ->with(['user', 'tracks.release'])
            ->where('id', $playlist->id)
            ->first();
    }
}
