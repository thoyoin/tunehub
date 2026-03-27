<?php

namespace App\Jobs;

use App\Services\MinioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class DeleteCoverFile implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public string $coverUrl,
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(MinioService $minioService): void
    {
        $minioService->destroyCover($this->coverUrl);
    }
}
