<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\LibraryItem\CreateLibraryItem;
use App\Actions\Release\CheckIfReleaseLiked;
use App\Actions\Track\StoreTrack;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class ReleaseService
{
    public function __construct(
        public MinioService $minioService,
        public StoreTrack $storeTrack,
        public CheckIfReleaseLiked $checkIfReleaseLiked,
        public CreateLibraryItem $createLibraryItem,
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
                    'type' => $releaseData['type'],
                    'artist' => $releaseData['artist'],
                    'cover_url' => $coverUrl,
                    'release_date' => $releaseData['release_date'],
                ]);

            $this->storeTrack->handle($files, $titles, $release);
        });
    }

    public function destroyByTrack($track): void
    {
        $release = $track->release()->withCount('tracks')->first();

        if ($release && $release->tracks_count === 1) {
            $this->minioService->destroyCover($track->cover_url);
            $release->delete();
        }
    }

    public function destroy($release): void
    {
        $releaseTracks = $release->tracks()->get();

        $release->delete();

        foreach ($releaseTracks as $track) {
            $this->minioService->destroyTrack($track);
        }
    }

    public function get($release)
    {
        if (auth()->check()) {
            $playlist = auth()
                ->user()
                ->playlists()
                ->where('slug', 'liked-tracks')
                ->first();

            $release->tracks->map(function ($track) use ($playlist) {
                return $track->is_added = (bool) $playlist->tracks->contains($track->id);
            });

            $isReleaseLiked = $this->checkIfReleaseLiked->handle($release);

            return [$release, $isReleaseLiked];
        } else {

            return $release->tracks;
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
            $coverUrl = $this->minioService->storeCover($request->file('cover_url'));

            $release->update([
                'title' => $request['releaseTitle'],
                'artist' => $request['artist'],
                'cover_url' => $coverUrl,
            ]);
        } else {
            $release->update([
                'title' => $request['releaseTitle'],
                'artist' => $request['artist'],
            ]);
        }
    }
}
