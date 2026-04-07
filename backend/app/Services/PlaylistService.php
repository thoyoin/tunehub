<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\LibraryItem\CreateLibraryItem;
use App\Actions\Playlist\AddTrackToPlaylist;
use App\Actions\Playlist\CountUserPlaylists;
use App\Actions\Playlist\GetOrderedPlaylistTracks;
use App\Actions\Playlist\GetPlaylistById;
use App\Actions\Playlist\GetUserLikedPlaylist;
use App\Actions\Track\IsTrackAdded;
use App\Jobs\DeleteCoverFile;
use App\Models\LibraryItem;
use App\Models\Playlist;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class PlaylistService
{
    public function __construct(
        public CountUserPlaylists $countUserPlaylists,
        public CreateLibraryItem $createLibraryItem,
        public GetPlaylistById $getPlaylistById,
        public GetOrderedPlaylistTracks $getOrderedPlaylistTracks,
        public GetUserLikedPlaylist $getUserLikedPlaylist,
        public MinioService $minioService,
        public AddTrackToPlaylist $addTrackToPlaylist,
        public IsTrackAdded $isTrackAdded,
        public TrackStatsService $trackStatsService,
    ) {}

    public function store(): LibraryItem
    {
        return DB::transaction(function () {
            $numberOfPlaylists = $this->countUserPlaylists->handle();

            $playlist = Playlist::create([
                'title' => 'My playlist'.' #'.($numberOfPlaylists + 1),
                'description' => null,
                'user_id' => auth()->id(),
                'cover_url' => config('media.defaults.playlist_cover_url'),
            ]);

            $libraryItem = $this->createLibraryItem->handle(auth()->id(), $playlist->id, 'playlist');

            Log::info('New Playlist was stored', [
                'title' => $playlist->title,
                'user_id' => $playlist->user_id,
                'visibility' => $playlist->visibility,
            ]);

            return $libraryItem->with('item')
                ->where('item_id', $playlist->id)
                ->with('user')
                ->first();
        });
    }

    public function get($playlist): array
    {
        $playlistItem = $this->getPlaylistById->handle($playlist);

        $orderedTracks = $this->getOrderedPlaylistTracks->handle($playlistItem->item);

        if (!$orderedTracks->isEmpty()) {
            $this->trackStatsService->getTracksPlays(
                $orderedTracks->pluck('id')->toArray(),
                $orderedTracks
            );
        }

        return [$playlistItem, $orderedTracks];
    }

    public function delete($playlist): void
    {
        DB::transaction(function () use ($playlist) {
            $url = $playlist->cover_url;

            $playlist->delete();

            $defaultCover = config('media.defaults.playlist_cover_url');

            DB::afterCommit(function () use ($url, $playlist, $defaultCover) {
                if ($url !== $defaultCover) {
                    DeleteCoverFile::dispatch($url);
                }

                Log::info('Playlist was deleted', [
                    'title' => $playlist->title,
                    'user_id' => $playlist->user_id,
                ]);
            });
        });
    }

    public function update($request, $playlist): Playlist
    {
        $data = $request->only(['title', 'description', 'cover_url', ]);

        if ($request->hasFile('cover_url')) {
            $url = $this->minioService->storeCover($request->file('cover_url'));
            $data['cover_url'] = $url;
        }

        $playlist->update($data);

        return $playlist;
    }

    public function updateVisibility(Playlist $playlist, Request $request)
    {
        $request->validate([
            'visibility' => 'required|in:public,private',
        ]);

        $playlist->visibility = $request->visibility;

        $playlist->save();

        return $playlist->visibility;
    }

    public function addTrack($playlist, $track): JsonResponse
    {
        $isTrackAdded = $this->isTrackAdded->handle($track, $playlist);

        return $this->addTrackToPlaylist->handle($track, $playlist, $isTrackAdded);
    }

    public function addTrackToLikes($track): JsonResponse
    {
        $likesPlaylist = $this->getUserLikedPlaylist->handle();

        $isTrackAdded = $this->isTrackAdded->handle($track, $likesPlaylist);

        return $this->addTrackToPlaylist->handle($track, $likesPlaylist, $isTrackAdded);
    }

    public function getAll()
    {
        return auth()->user()->playlists()->get();
    }
}
