<?php

declare(strict_types=1);

namespace App\Actions\AdminPanel\playlist;

use App\Models\Playlist;

class GetAllPlaylists
{
    public function handle(): array
    {
        $allPlaylists = Playlist::query()
            ->with(['tracks', 'user'])
            ->whereNot('slug', 'liked-tracks')
            ->paginate(10);

        $hiddenPlaylists = Playlist::query()
            ->with(['tracks', 'user'])
            ->where('visibility', 'private')
            ->whereNot('slug', 'liked-tracks')
            ->paginate(10);

        return [$allPlaylists, $hiddenPlaylists];
    }
}
