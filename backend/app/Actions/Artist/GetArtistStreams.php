<?php

declare(strict_types=1);

namespace App\Actions\Artist;

use ClickHouseDB\Client;
use Illuminate\Support\Facades\Auth;

class GetArtistStreams
{
    public function __construct(
        public Client $clickhouse
    )
    {}

    public function handle()
    {
        $artistId = Auth::id();

        $rows = $this->clickhouse->select("
            SELECT
                sum(plays) AS streams
                FROM artist_streams
            WHERE artist_id = $artistId
        ")->rows();

        return collect($rows)->first()['streams'] ?? 0;
    }
}
