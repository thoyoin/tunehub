<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Artist\GetArtistStreams;
use App\Actions\Release\GetUserReleases;
use App\Actions\Track\GetUserTracks;
use ClickHouseDB\Client;
use Illuminate\Support\Facades\Auth;

class ArtistStudioService
{
    public function __construct(
        public Client $clickhouse,
        public GetArtistStreams $getArtistStreams,
        public GetUserTracks $getUserTracks,
        public GetUserReleases $getUserReleases,
    )
    {}

    public function getArtistStats()
    {
        return $this->getArtistStreams->handle();
    }

    public function getArtistEarnings()
    {
        $artistId = Auth::id();

        $rows = $this->clickhouse->select("
            SELECT
                formatDateTime(date, '%b %d') as date,
                earnings
            FROM
                artist_earnings_daily
            WHERE
                artist_id = $artistId
            ORDER BY
                date DESC
        ")->rows();

        $date = array_column($rows, 'date');
        $earnings = array_column($rows, 'earnings');

        $totalEarnings = array_sum($earnings);

        return [
            'date' => $date,
            'earnings' => $earnings,
            'total_earnings' => $totalEarnings,
        ];
    }

    public function getDailyStreams(): array
    {
        $artistId = Auth::id();

        return $this->clickhouse->select("
            SELECT
                formatDateTime(date, '%b %d, %Y') as date,
                plays
                FROM
                    artist_earnings_daily
            WHERE
                artist_id = :artist_id
            ORDER BY
                date ASC
        ",['artist_id' => $artistId])->rows();
    }
}
