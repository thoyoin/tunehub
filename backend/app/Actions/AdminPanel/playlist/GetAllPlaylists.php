<?php

declare(strict_types=1);

namespace App\Actions\AdminPanel\playlist;

use App\Models\Playlist;
use Illuminate\Pagination\LengthAwarePaginator;

class GetAllPlaylists
{
    public function handle(): LengthAwarePaginator
    {
        return Playlist::query()
            ->with(['tracks', 'user'])
            ->whereNot('slug', 'liked-tracks')
            ->paginate(10);
    }
}
