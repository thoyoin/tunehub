<?php

namespace App\Listeners;

use App\Events\TrackListened;
use ClickHouseDB\Client;
use Illuminate\Contracts\Queue\ShouldQueue;

class TrackPlayed implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct(
        public Client $clickhouse
    )
    {}

    /**
     * Handle the event.
     */
    public function handle(TrackListened $event): void
    {
        if ($event->trackArtistId === $event->userId) {
            return;
        }

        if (!is_numeric($event->trackId) || (int)$event->trackId <= 0) {
            return;
        }

        if (!is_numeric($event->userId) || (int)$event->userId <= 0) {
            return;
        }

        if (!is_numeric($event->trackArtistId) || (int)$event->trackArtistId <= 0) {
            return;
        }

        if (!is_numeric($event->releaseId) || (int)$event->releaseId <= 0) {
            return;
        }

        $this->clickhouse->insert('track_plays', [
            [
                $event->trackId,
                $event->userId,
                now()->timestamp,
                $event->trackArtistId,
                $event->releaseId
            ]
        ], ['track_id', 'user_id', 'played_at', 'track_artist_id', 'release_id']);
    }
}
