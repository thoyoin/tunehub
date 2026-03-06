<?php

namespace App\Jobs;

use App\Services\MinioService;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\SerializesModels;

class DeleteTrackAudioFile implements ShouldQueue
{
    use Queueable, SerializesModels;

    public int $tries = 3;
    public int $timeout = 60;

    protected string $audioPath;

    /**
     * Create a new job instance.
     */
    public function __construct($audioPath)
    {
        $this->audioPath = $audioPath;
    }

    /**
     * Execute the job.
     */
    public function handle(MinioService $minioService): void
    {
        $minioService->destroyTrack($this->audioPath);
    }
}
