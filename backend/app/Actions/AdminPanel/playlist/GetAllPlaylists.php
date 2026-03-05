<?php

declare(strict_types=1);

namespace App\Actions\AdminPanel\playlist;

use App\Models\Playlist;

class GetAllPlaylists
{
    public function handle($request): array
    {
        $search = $request->query('query');

        $allPlaylists = Playlist::query()
            ->with(['tracks' => fn ($query) => $query->orderBy('pivot_position')])
            ->with('user')
            ->whereNot('slug', 'liked-tracks')
            ->when($search, fn ($query) =>
                $query->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($query) => $query
                        ->where('username', 'like', "%{$search}%"))
            )
            ->paginate(10);

        $privatePlaylists = Playlist::query()
            ->with(['tracks' => fn ($query) => $query->orderBy('pivot_position')])
            ->with('user')
            ->whereNot('slug', 'liked-tracks')
            ->when($search, fn ($query) =>
                $query->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($query) => $query
                        ->where('username', 'like', "%{$search}%"))
            )
            ->where('visibility', 'private')
            ->paginate(10);

        $hiddenPlaylists = Playlist::query()
            ->with(['tracks' => fn ($query) => $query->orderBy('pivot_position')])
            ->with('user')
            ->whereNot('slug', 'liked-tracks')
            ->when($search, fn ($query) =>
                $query->where('title', 'like', "%{$search}%")
                    ->orWhereHas('user', fn($query) => $query
                        ->where('username', 'like', "%{$search}%"))
            )
            ->where('is_hidden', true)
            ->paginate(10);

        return [$allPlaylists, $privatePlaylists, $hiddenPlaylists];
    }
}
