<?php

declare(strict_types=1);

namespace App\Services;

use Illuminate\Support\Facades\DB;

class DestroyTrackService
{
    public function __construct(
        public TrackService $trackService,
        public ReleaseService $releaseService
    ) {}

    public function handle($track): void
    {
        DB::transaction(function () use ($track) {
            $this->releaseService->destroy($track);

            $this->trackService->destroy($track);
        });
    }
}
