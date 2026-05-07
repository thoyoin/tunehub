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
        $ids = Release::where('user_id', $artistId)
            ->pluck('id')
            ->all();

        if (empty($ids)) {
            return collect();
        }

        $idsList = collect($ids)
            ->map(fn ($id) => (int) $id)
            ->filter(fn ($id) => $id > 0)
            ->implode(',');

        if ($idsList === '') {
            return collect();
        }

        $rows = $this->clickhouse->select("
            select
                release_id,
                sum(plays) as plays
                from release_streams
                where release_id IN ({$idsList})
            group by release_id
            order by plays desc
        ")->rows();

        $releases = Release::whereIn('id', array_column($rows, 'release_id'))
            ->get()
            ->keyBy('id');

        return collect($rows)
            ->filter(fn ($row) => $releases->has((int) $row['release_id']))
            ->map(function ($row) use ($releases) {
                $release = $releases->get((int)$row['release_id']);
                $release->plays = (int)$row['plays'];
                return $release;
            })
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
