<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\LibraryItem\CreateLibraryItem;
use App\Actions\Playlist\CountUserPlaylists;
use App\Actions\Playlist\GetOrderedPlaylistTracks;
use App\Actions\Playlist\GetPlaylistById;
use App\Models\LibraryItem;
use App\Models\Playlist;
use Illuminate\Support\Facades\DB;

class PlaylistService
{
    public function __construct(
        public CountUserPlaylists $countUserPlaylists,
        public CreateLibraryItem $createLibraryItem,
        public GetPlaylistById $getPlaylistById,
        public GetOrderedPlaylistTracks $getOrderedPlaylistTracks,
        public MinioService $minioService,
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

            $libraryItem = $this->createLibraryItem->handle(auth()->id(), $playlist->id, 'playlist');

            return $libraryItem->with('item')->where('item_id', $playlist->id)->first();
        });
    }

    public function get($playlist): array
    {
        $playlist = $this->getPlaylistById->handle($playlist);

        $tracks = $this->getOrderedPlaylistTracks->handle($playlist);

        $tracks = $tracks->map(function ($track) {
            $track->is_added = true;

            return $track;
        });

        return [$playlist, $tracks];
    }

    public function delete($playlist): void
    {
        $url = $playlist->cover_url;

        $playlist->delete();

        $defaultCover = 'http://localhost:9000/tunehub/defaults/default_cover.png';

        if ($url !== $defaultCover) {
            $this->minioService->destroyCover($url);
        }
    }

    public function update($request, $playlist): Playlist
    {
        $data = $request->only(['title', 'description', 'cover_url']);

        if ($request->hasFile('cover_url')) {
            $url = $this->minioService->storeCover($request->file('cover_url'));
            $data['cover_url'] = $url;
        }

        $playlist->update($data);

        return $playlist;
    }
}
