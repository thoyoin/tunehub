<?php

declare(strict_types=1);

namespace App\Services;

class ReleaseService
{
    public function __construct(
        public MinioService $minioService,
    ) {}

    public function store($data)
    {
        $coverUrl = $this->minioService->storeCover($data['cover_url']);

        return auth()
            ->user()
            ->releases()
            ->create([
                'title' => $data['releaseTitle'],
                'type' => $data['type'],
                'artist' => $data['artist'],
                'cover_url' => $coverUrl,
                'release_date' => $data['release_date'],
            ]);
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
