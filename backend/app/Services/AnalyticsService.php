<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\AdminPanel\Overview\CountGrowthPercentage;
use App\Models\Playlist;
use App\Models\Release;
use App\Models\Track;
use App\Models\User;
use ClickHouseDB\Client;

class AnalyticsService
{
    public function __construct(
        public Client $clickhouse,
        public CountGrowthPercentage $countGrowth,
    )
    {}

    public function getTotalPlays(): array
    {
        $currentMonth = $this->clickhouse->select(
            "SELECT count() as total_plays from track_plays where played_at >= now() - INTERVAL 30 DAY"
        );

        $pastMonth = $this->clickhouse->select(
            "SELECT count() as total_plays FROM track_plays WHERE played_at >= now() - INTERVAL 60 DAY
                                         AND played_at < now() - INTERVAL 30 DAY"
        );

        $currentMonthResult = (int) $currentMonth->rows()[0]['total_plays'];

        $pastMonthResult = (int) $pastMonth->rows()[0]['total_plays'];

        if ($pastMonthResult == 0) {
            $growth = $currentMonthResult > 0 ? 100 : 0;
        } else {
            $growth = ($currentMonthResult - $pastMonthResult) / $pastMonthResult * 100;
        }

        return [$currentMonthResult, $growth];
    }

    public function getNewUsers(): array
    {
        $currentMonth = (int) User::where('created_at', '>', now()->subDays(30))
            ->count();

        $pastMonth = (int) User::where('created_at', '>', now()->subDays(60))
            ->where('created_at', '<=', now()->subDays(30))
            ->count();

        $growth = $this->countGrowth->handle($currentMonth, $pastMonth);

        return [$currentMonth, $growth];
    }

    public function getNewTracks(): array
    {
        $currentMonth = Track::where('release_date', '>', now()->subDays(30))
            ->count();

        $pastMonth = Track::where('release_date', '>', now()->subDays(60))
            ->where('release_date', '<=', now()->subDays(30))
            ->count();

        $growth = $this->countGrowth->handle($currentMonth, $pastMonth);

        return [$currentMonth, $growth];
    }

    public function getNewReleases(): array
    {
        $currentMonth = Release::where('release_date', '>', now()->subDays(30))
            ->count();

        $pastMonth = Release::where('release_date', '>', now()->subDays(60))
            ->where('release_date', '<=', now()->subDays(30))
            ->count();

        $growth = $this->countGrowth->handle($currentMonth, $pastMonth);

        return [$currentMonth, $growth];
    }

    public function getNewPlaylists(): array
    {
        $currentMonth = Playlist::where('created_at', '>', now()->subDays(30))
            ->whereNot('slug', 'liked-tracks')
            ->count();

        $pastMonth = Playlist::where('created_at', '>', now()->subDays(60))
            ->where('created_at', '<=', now()->subDays(30))
            ->count();

        $growth = $this->countGrowth->handle($currentMonth, $pastMonth);

        return [$currentMonth, $growth];
    }
}
