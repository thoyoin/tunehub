<?php

declare(strict_types=1);

namespace App\Services;

use getID3;
use Illuminate\Support\Facades\Auth;

class TrackService
{
    public function __construct(
        public MinioService $minioService,
        public getID3 $getID3,
    ) {}

    public function store(
        $files,
        $titles,
        $release,
    ): void {
        foreach ($files as $index => $file) {
            $title = $titles[$index];
            $fileInfo = $this->getID3->analyze($file->getPathname());

            $duration = round($fileInfo['playtime_seconds']);
            $audioUrl = $this->minioService->storeTrack($file);

            $release->tracks()->create([
                'title' => $title,
                'artist' => $release->artist,
                'user_id' => Auth::id(),
                'release_date' => $release->release_date,
                'audio_url' => $audioUrl,
                'cover_url' => $release->cover_url,
                'duration' => $duration,
                'position' => $index + 1,
            ]);
        }
    }
}
