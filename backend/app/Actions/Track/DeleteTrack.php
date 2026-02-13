<?php

declare(strict_types=1);

namespace App\Actions\Track;

use App\Services\MinioService;

class DeleteTrack
{
    public function __construct(
        public MinioService $minioService,
    ) {}

    public function handle($track): void
    {
        $this->minioService->destroyTrack($track);

        $track->delete();
    }
}
