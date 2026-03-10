<?php

namespace App\Jobs;

use ClickHouseDB\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Queue\Queueable;

class DeleteTrackInClickhouse implements ShouldQueue
{
    use Queueable;

    public int $tries = 3;
    public int $timeout = 60;

    protected int $trackId;

    /**
     * Create a new job instance.
     */
    public function __construct(int $trackId)
    {
        $this->trackId = $trackId;
    }

    /**
     * Execute the job.
     */
    public function handle(Client $clickhouse): void
    {
        $clickhouse->write("
                ALTER TABLE track_plays DELETE WHERE track_id = {$this->trackId}
            ");

        $clickhouse->write("
                ALTER TABLE track_plays_total DELETE WHERE track_id = {$this->trackId}
            ");
    }
}
