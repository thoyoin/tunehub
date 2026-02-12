<?php

declare(strict_types=1);

namespace App\Actions\Track;

use App\Actions\Playlist\GetUserLikedPlaylist;
use App\Models\Track;

class IsTrackLiked
{
    public function __construct(
        public GetUserLikedPlaylist $getUserLikedPlaylist,
    ){}

    public function handle(Track $track): bool
    {
        $likedPlaylist = $this->getUserLikedPlaylist->handle();

        return $likedPlaylist->tracks()->whereKey($track->id)->exists();
    }
}
