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
    {}

    /**
     * Handle the event.
     */
    public function handle(TrackListened $event): void
    {
        if (!in_array($event->itemType, ['release', 'playlist'], true)) {
            return;
        }

        if (!is_numeric($event->itemId) || (int)$event->itemId <= 0) {
            return;
        }

        if (!is_numeric($event->userId) || (int)$event->userId <= 0) {
            return;
        }

        $key = "user:{$event->userId}:recentlyPlayed";

        Redis::zadd(
            $key,
            now()->timestamp,
            "{$event->itemType}:{$event->itemId}"
        );
    }
}
