<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Release;
use App\Models\User;
use ClickHouseDB\Client;

class ArtistService
{
    public function __construct(
        public Client $clickhouse
    )
    {}

    public function getAlbums($artistId)
    {
        $artistReleases = Release::where('user_id', $artistId)
            ->get()
            ->pluck('id')
            ->implode(', ');

        $rows = $this->clickhouse->select("
            select
                release_id,
                sum(plays) as plays
                from release_streams
                where release_id IN ($artistReleases)
            group by release_id
            order by plays desc
        ")->rows();


        $releases = Release::whereIn('id', array_column($rows, 'release_id'))
            ->get()
            ->keyBy('id');

        foreach ($rows as $row) {
            $releases[$row['release_id']]->plays = $row['plays'];
        }

        return collect(array_column($rows, 'release_id'))
            ->map(fn ($id) => $releases[$id] ?? null)
            ->filter()
            ->values();
    }

    public function getArtist($artistId)
    {
        return User::where('id', $artistId)
            ->with(['products' => function ($query) {
                $query->where('status', 'active')
                    ->with('productVariants');
            }])
            ->first();
    }
}
