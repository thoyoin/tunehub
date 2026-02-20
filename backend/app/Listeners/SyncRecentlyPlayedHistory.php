<?php

namespace App\Listeners;

use App\Events\TrackListened;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class SyncRecentlyPlayedHistory implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(TrackListened $event): void
    {
        $key = "user:{$event->userId}:recentlyPlayed";

        Redis::zadd(
            $key,
            now()->timestamp,
            "{$event->itemType}:{$event->itemId}");
    }
}
