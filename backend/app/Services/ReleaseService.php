<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\LibraryItem\CreateLibraryItem;
use App\Actions\Playlist\GetUserLikedPlaylist;
use App\Actions\Release\CheckIfReleaseLiked;
use App\Actions\Track\StoreTrack;
use App\Jobs\DeleteCoverFile;
use App\Jobs\DeleteTrackAudioFile;
use App\Models\Release;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ReleaseService
{
    public function __construct(
        public MinioService $minioService,
        public StoreTrack $storeTrack,
        public CheckIfReleaseLiked $checkIfReleaseLiked,
        public CreateLibraryItem $createLibraryItem,
        public GetUserLikedPlaylist $getUserLikedPlaylist,
        public TrackStatsService $trackStatsService,
    ) {}

    public function store($request): void
    {
        DB::transaction(function () use ($request) {
            $releaseData = $request->only([
                'releaseTitle',
                'artist',
                'type',
                'cover_url',
                'release_date'
            ]);

            $files = $request->file('audio_url');
            $titles = $request->input('title');

            $coverUrl = $this->minioService->storeCover($releaseData['cover_url']);

            $release = auth()
                ->user()
                ->releases()
                ->create([
                    'title' => $releaseData['releaseTitle'],
                    'release_type' => $releaseData['type'],
                    'artist' => $releaseData['artist'],
                    'cover_url' => $coverUrl,
                    'release_date' => $releaseData['release_date'],
                ]);

            $this->storeTrack->handle($files, $titles, $release);

            Log::info('New Release was stored', [
                'release_id' => $release->id,
                'title' => $release->title,
                'artist' => $release->artist,
            ]);
        });
    }

    public function destroyByTrack($track): void
    {
        DB::transaction(function () use ($track) {
            $release = $track->release()->withCount('tracks')->first();

            if ($release && $release->tracks_count === 1) {
                $release->delete();

                DB::afterCommit(function () use ($release) {
                    DeleteCoverFile::dispatch($release->cover_url);

                    Log::info('Release was deleted', [
                        'title' => $release->title,
                        'artist' => $release->artist,
                    ]);
                });

            } else {
                $currentPosition = $track->position;

                DB::table('tracks')
                    ->where('release_id', $release->id)
                    ->where('position', '>', $currentPosition)
                    ->decrement('position');
            }
        });
    }

    public function destroy($release): void
    {
        DB::transaction(function () use ($release) {
            $releaseTracks = $release->tracks()->get();

            $release->delete();

            DB::afterCommit(function () use ($releaseTracks, $release) {
                foreach ($releaseTracks as $track) {
                    $audioUrl = $track->audio_url;
                    DeleteTrackAudioFile::dispatch($audioUrl);
                }

                DeleteCoverFile::dispatch($release->cover_url);

                Log::info('Release was deleted', [
                    'title' => $release->title,
                    'artist' => $release->artist,
                ]);
            });
        });
    }

    public function get($release): array
    {
        if (auth()->check()) {
            $likesPlaylist = $this->getUserLikedPlaylist->handle();

            $tracks = $release
                ->tracks()
                ->withExists([
                    'playlists as is_liked' => fn ($q) =>
                    $q->whereKey($likesPlaylist->id),
                ])
                ->get();

            $tracks->load('playlists:id');

            $this->trackStatsService->getTracksPlays(
                $tracks->pluck('id')->toArray(),
                $tracks
            );

            $isReleaseLiked = $this->checkIfReleaseLiked->handle($release);

            $release->isReleaseLiked = $isReleaseLiked;

            return [$release, $tracks];
        } else {

            $tracks = $release->tracks()->get();

            $this->trackStatsService->getTracksPlays(
                $tracks->pluck('id')->toArray(),
                $tracks
            );

            return [$release, $tracks];
        }
    }

    public function addToLikes($release): JsonResponse
    {
        $libraryItem = auth()
            ->user()
            ->libraryItems()
            ->where('item_type', 'release')
            ->where('item_id', $release->id)
            ->first();

        if (! $libraryItem) {
            $this->createLibraryItem->handle(auth()->id(), $release->id, 'release');

            return response()->json([
                'liked' => true,
            ]);
        } else {
            $libraryItem->delete();

            return response()->json([
                'liked' => false,
            ]);
        }
    }

    public function update($release, $request): void
    {
        if ($request->hasFile('cover_url')) {
            DB::transaction(function () use ($release, $request) {
                $coverUrl = $this->minioService->storeCover($request->file('cover_url'));

                $release->update([
                    'title' => $request['releaseTitle'],
                    'artist' => $request['artist'],
                    'cover_url' => $coverUrl,
                ]);

                $release->tracks()->update([
                    'cover_url' => $coverUrl,
                ]);
            });
        } else {
            $release->update([
                'title' => $request['releaseTitle'],
                'artist' => $request['artist'],
            ]);
        }
    }

    public function publish($release): void
    {
        $release->status = 'published';

        $release->save();

        Log::info('New Release was published', [
            'title' => $release->title,
            'artist' => $release->artist,
        ]);
    }

    public function getArtistLatest($artistId): Release | null
    {
        return Release::where('user_id', $artistId)
            ->where('status', 'published')
            ->latest()
            ->with('tracks')
            ->first();
    }
}
