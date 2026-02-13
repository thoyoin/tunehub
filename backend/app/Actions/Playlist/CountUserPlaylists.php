<?php

declare(strict_types=1);

namespace App\Actions\Playlist;

use App\Models\Playlist;

class CountUserPlaylists
{
    public function handle()
    {
        $baseTitle = 'My Playlist';

        return Playlist::where('user_id', auth()->id())
            ->where('title', 'like', $baseTitle.'%')
            ->count();
    }
}
