<?php

declare(strict_types=1);

namespace App\Actions\AdminPanel\playlist;

use App\Models\Playlist;

class GetAllPlaylists
{
    public function handle(): array
    {
        $allPlaylists = Playlist::query()
            ->with(['tracks' => fn ($query) => $query->orderBy('pivot_position')])
            ->with('user')
            ->whereNot('slug', 'liked-tracks')
            ->paginate(10);

        $privatePlaylists = Playlist::query()
            ->with(['tracks' => fn ($query) => $query->orderBy('pivot_position')])
            ->with('user')
            ->where('visibility', 'private')
            ->whereNot('slug', 'liked-tracks')
            ->paginate(10);

        $hiddenPlaylists = Playlist::query()
            ->with(['tracks' => fn ($query) => $query->orderBy('pivot_position')])
            ->with('user')
            ->where('is_hidden', true)
            ->whereNot('slug', 'liked-tracks')
            ->paginate(10);

        return [$allPlaylists, $privatePlaylists, $hiddenPlaylists];
    }
}
