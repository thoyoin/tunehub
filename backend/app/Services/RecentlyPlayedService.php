<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\TrackListened;
use App\Models\Playlist;
use App\Models\Release;
use App\Models\Track;
use Illuminate\Support\Facades\Redis;

class RecentlyPlayedService
{
    public function store($data): void
    {
        $track = Track::query()
            ->whereKey($data['track_id'])
            ->where('release_id', $data['release_id'])
            ->with('release')
            ->firstOrFail();

        if ($track->user_id === auth()->id()) {
            return;
        }

        if (!$this->canCountListen(auth()->id(), $track->id)) {
            return;
        }

        TrackListened::dispatch(
            auth()->id(),
            $track->release_id,
            $track->id,
            $track->release->item_type,
            $track->user_id,
        );
    }

    public function canCountListen(int $userId, int $trackId): bool
    {
        $windowSeconds = 60;
        $maxListensPerWindow = 1;
        $now = now()->timestamp;
        $key = "listen-rate:{$userId}:{$trackId}";
        $windowStart = $now - $windowSeconds;

        Redis::zremrangebyscore($key, '-inf', (string) $windowStart);

        $listenCount = Redis::zcard($key);

        if ($listenCount >= $maxListensPerWindow) {
            Redis::expire($key, $windowSeconds);

            return false;
        }

        Redis::zadd($key, $now, (string) $now);
        Redis::expire($key, $windowSeconds);

        return true;
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
