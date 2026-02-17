<?php

declare(strict_types=1);

namespace App\Actions\Playlist;

class GetOrderedPlaylistTracks
{
    public function __construct(
        public GetUserLikedPlaylist $getUserLikedPlaylist,
    ) {}

    public function handle($playlist)
    {
        $likesPlaylist = $this->getUserLikedPlaylist->handle();

        $tracks = $playlist
            ->tracks()
            ->orderBy('pivot_position')
            ->with('release')
            ->withExists([
                'playlists as is_liked' => fn ($q) =>
                    $q->whereKey($likesPlaylist->id),
            ])
            ->get();

        return $tracks->load('playlists:id');
    }
}
