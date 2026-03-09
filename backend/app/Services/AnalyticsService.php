<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\User;
use ClickHouseDB\Client;

class AnalyticsService
{
    public function __construct(
        public Client $clickhouse
    )
    {}

    public function getTotalPlays(): int
    {
        $result = $this->clickhouse->select(
            "SELECT count() as total_plays from track_plays where played_at >= now() - INTERVAL 30 DAY"
        );

        return (int) $result->rows()[0]['total_plays'];
    }

    public function getNewUsers(): int
    {
        return User::where('created_at', '>', now()->subDays(30))
            ->count();
    }
}
