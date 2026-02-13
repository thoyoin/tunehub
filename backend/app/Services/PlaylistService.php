<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\LibraryItem\CreateLibraryItem;
use App\Actions\Playlist\CountUserPlaylists;
use App\Models\LibraryItem;
use App\Models\Playlist;
use Illuminate\Support\Facades\DB;

class PlaylistService
{
    public function __construct(
        public CountUserPlaylists $countUserPlaylists,
        public CreateLibraryItem $createLibraryItem,
    ) {}

    public function store(): LibraryItem
    {
        return DB::transaction(function () {
            $numberOfPlaylists = $this->countUserPlaylists->handle();

            $playlist = Playlist::create([
                'title' => 'My Playlist'.' #'.($numberOfPlaylists + 1),
                'description' => null,
                'user_id' => auth()->id(),
                'cover_url' => 'http://localhost:9000/tunehub/defaults/default_cover.png',
            ]);

            $libraryItem = $this->createLibraryItem->handle(auth()->id(), $playlist->id);

            return $libraryItem->with('item')->where('item_id', $playlist->id)->first();
        });
    }
}
