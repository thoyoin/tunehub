<?php

declare(strict_types=1);

namespace App\Actions\Track;

use App\Models\Playlist;
use App\Models\Track;

class IsTrackAdded
{
    public function handle(Track $track, Playlist $playlist): bool
    {
        return $playlist->tracks->contains($track->id);
    }
}
