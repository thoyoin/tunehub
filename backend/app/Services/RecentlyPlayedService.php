<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\TrackListened;
use App\Models\Playlist;
use App\Models\Release;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Redis;

class RecentlyPlayedService
{
    public function store($data): JsonResponse
    {
        TrackListened::dispatch(auth()->id(), $data['id'], $data['item_type']);

        return response()->json([
            'sentData' => $data,
        ]);
    }

    public function get(): ?array
    {
        $auth = auth()->id();

        $key = "user:{$auth}:recentlyPlayed";

        $items = Redis::zrevrange($key, 0, -1);

        $grouped = [
            'release' => [],
            'playlist' => [],
        ];

        foreach ($items as $item) {
            [$type, $id] = explode(':', $item);
            $grouped[$type][] = (int) $id;
        }

        $releases = Release::whereIn('id', $grouped['release'])
            ->with('tracks')
            ->get()
            ->keyBy('id');

        $playlists = Playlist::whereIn('id', $grouped['playlist'])
            ->with('tracks')
            ->get()
            ->keyBy('id');

        $result = [];

        foreach ($items as $item) {
            [$type, $id] = explode(':', $item);

            if ($type === 'release' && isset($releases[$id])) {
                $result[] = $releases[$id];
            }

            if ($type === 'playlist' && isset($playlists[$id])) {
                $result[] = $playlists[$id];
            }
        }

        return $result;
    }
}
