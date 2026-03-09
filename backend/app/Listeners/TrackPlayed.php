<?php

namespace App\Listeners;

use App\Events\TrackListened;
use ClickHouseDB\Client;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Redis;

class TrackPlayed implements ShouldQueue
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
        $client = new Client([
            'host' => env('clickhouse.host', 'tunehub-clickhouse'),
            'port' => env('clickhouse.port', 8123),
            'username' => env('clickhouse.user', 'default'),
            'password' => env('clickhouse.password', 'default'),
        ]);

        $client->database('default');

        $client->insert('track_plays', [
            [
                $event->trackId,
                $event->userId,
                now()->timestamp,
            ]
        ], ['track_id', 'user_id', 'played_at']);
    }
}
