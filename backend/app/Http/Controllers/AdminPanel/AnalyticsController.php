<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminPanel;

use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;

use function Termwind\parse;

class AnalyticsController
{
    public function getTotalPlays(AnalyticsService $analytics): JsonResponse
    {
        [$totalPlays, $growth] = $analytics->getTotalPlays();

        return response()->json([
            'totalPlays' => $totalPlays,
            'growth' => $growth,
        ]);
    }

    public function getNewUsers(AnalyticsService $analytics): JsonResponse
    {
        [$newUsers, $growth] = $analytics->getNewUsers();

        return response()->json([
            'newUsers' => $newUsers,
            'growth' => $growth,
        ]);
    }

    public function getNewTracks(AnalyticsService $analytics): JsonResponse
    {
        [$newTracks, $growth] = $analytics->getNewTracks();

        return response()->json([
            'newTracks' => $newTracks,
            'growth' => $growth,
        ]);
    }

    public function getNewReleases(AnalyticsService $analytics): JsonResponse
    {
        [$newReleases, $growth] = $analytics->getNewReleases();

        return response()->json([
            'newReleases' => $newReleases,
            'growth' => $growth,
        ]);
    }

    public function getNewPlaylists(AnalyticsService $analytics): JsonResponse
    {
        [$newPlaylists, $growth] = $analytics->getNewPlaylists();

        return response()->json([
            'newPlaylists' => $newPlaylists,
            'growth' => $growth,
        ]);
    }
}
