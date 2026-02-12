<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

class UploadReleaseService
{
    public function __construct(
        public TrackService $trackService,
        public ReleaseService $releaseService,
    ) {}

    public function handle($request)
    {
        return DB::transaction(function () use ($request) {
            $releaseData = $request->only([
                'releaseTitle',
                'artist',
                'type',
                'cover_url',
                'release_date'
            ]);

            $files = $request->file('audio_url');
            $titles = $request->input('title');

            $release = $this->releaseService->store($releaseData);

            $this->trackService->store($files, $titles, $release);

            return $release;
        });
    }
}
