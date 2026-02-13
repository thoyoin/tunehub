<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Track\StoreTrack;
use Illuminate\Support\Facades\DB;

class ReleaseService
{
    public function __construct(
        public MinioService $minioService,
        public StoreTrack $storeTrack,
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

    public function destroy($track): void
    {
        $release = $track->release()->withCount('tracks')->first();

        if ($release && $release->tracks_count === 1) {
            $this->minioService->destroyCover($track->cover_url);
            $release->delete();
        }
    }
}
