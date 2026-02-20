<?php

namespace App\Listeners;

use App\Events\TrackListened;
use App\Models\RecentlyPlayed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

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
        RecentlyPlayed::updateOrCreate(
            [
                'user_id' => $event->userId,
                'item_type' => $event->itemType,
                'item_id' => $event->itemId,
            ],
            [
                'played_at' => now(),
            ]
        );
    }
}
