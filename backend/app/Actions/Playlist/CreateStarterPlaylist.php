<?php

declare(strict_types=1);

namespace App\Actions\Playlist;

use App\Models\Playlist;

class CreateStarterPlaylist
{
    public function handle($user)
    {
         return Playlist::create([
             'title' => 'Liked tracks',
             'description' => null,
             'user_id' => $user->id,
             'cover_url' => config('media.defaults.liked_playlist_cover_url'),
             'visibility' => 'private',
        ]);
    }
}
