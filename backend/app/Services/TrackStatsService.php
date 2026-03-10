<?php

declare(strict_types=1);

namespace App\Services;

use ClickHouseDB\Client;
use Illuminate\Database\Eloquent\Collection;

class TrackStatsService
{
    public function __construct(
        public Client $clickhouse
    )
    {}

    public function getTracksPlays(array $tracksIds, $tracks): Collection
    {
        $ids = implode(',', $tracksIds);

        $result = $this->clickhouse->select("
            SELECT track_id, sum(plays) as plays
            FROM track_plays_total
            WHERE track_id IN ($ids)
            GROUP BY track_id
        ");

        $plays = [];

        foreach ($result->rows() as $row) {
            $plays[$row['track_id']] = (int) $row['plays'];
        }

        foreach ($tracks as $track) {
            $track->plays = $plays[$track->id] ?? 0;
        };

        return $tracks;
    }
}
