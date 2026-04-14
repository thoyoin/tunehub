<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\AdminPanel\Overview\CountGrowthPercentage;
use App\Models\Playlist;
use App\Models\Release;
use App\Models\Track;
use App\Models\User;
use ClickHouseDB\Client;
use Illuminate\Support\Collection;

class AnalyticsService
{
    public function __construct(
        public Client $clickhouse,
        public CountGrowthPercentage $countGrowth,
    )
    {}

    public function getTotalPlays(): array
    {
        $currentMonth = $this->clickhouse->select("
            SELECT count() as total_plays
            FROM track_plays
            WHERE played_at >= now() - INTERVAL 30 DAY
        ");

        $pastMonth = $this->clickhouse->select("
            SELECT count() as total_plays
            FROM track_plays
            WHERE played_at >= now() - INTERVAL 60 DAY
            AND played_at < now() - INTERVAL 30 DAY
        ");

        $currentMonthResult = (int) $currentMonth->rows()[0]['total_plays'];

        $pastMonthResult = (int) $pastMonth->rows()[0]['total_plays'];

        $growth = $this->countGrowth->handle($currentMonthResult, $pastMonthResult);

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

    public function getMonthPlays(): array
    {
        $result = $this->clickhouse->select("
            SELECT
                formatDateTime(played_at, '%b %d') as date,
                count() as plays
            FROM track_plays
            WHERE played_at >= now() - INTERVAL 30 DAY
            GROUP BY date
            ORDER BY date desc
        ");

        return $result->rows();
    }

    public function getUserGrowth(): array
    {
        return User::query()
            ->selectRaw('DATE(created_at) as full_date, DATE_FORMAT(created_at, "%b %d") as date, count(*) as users')
            ->where('created_at', '>=', now()->subDays(29)->startOfDay())
            ->groupByRaw('DATE(created_at), DATE_FORMAT(created_at, "%b %d")')
            ->orderBy('full_date')
            ->get()
            ->toArray();
    }

    public function getTopArtists(): Collection
    {
        $top = $this->clickhouse->select("
            SELECT
                artist_id,
                sum(plays) AS streams
                FROM artist_streams
            GROUP BY artist_id
            ORDER BY streams DESC
            LIMIT 10
        ")->rows();

        $artists = User::whereIn('id', array_column($top, 'artist_id'))
            ->with('roles', 'tracks', 'playlists')
            ->get()
            ->keyBy('id');

        return collect($top)->map(function ($row) use ($artists) {
            $artist = $artists[$row['artist_id']];

            return [
                'artist' => $artist,
                'streams' => $row['streams'],
            ];
        });
    }

    public function getTopReleases(): Collection
    {
        $rows = $this->clickhouse->select("
            SELECT
                release_id,
                sum(plays) AS plays
                FROM release_streams
            GROUP BY release_id
            ORDER BY plays DESC
            LIMIT 10
        ")->rows();

        $releases = Release::whereIn('id', array_column($rows, 'release_id'))
            ->with('user', 'tracks')
            ->get()
            ->keyBy('id');

        foreach ($rows as $row) {
            $releases[$row['release_id']]->plays = $row['plays'];
        }

        return collect(array_column($rows, 'release_id'))
            ->map(fn ($id) => $releases[$id])
            ->values();
    }
}
