<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminPanel;

use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;

use function Termwind\parse;

class AnalyticsController
{
    public function __construct(
        public AnalyticsService $analyticsService
    )
    {}

    public function getTotalPlays(): JsonResponse
    {
        [$totalPlays, $growth] = $this->analyticsService->getTotalPlays();

        return response()->json([
            'totalPlays' => $totalPlays,
            'growth' => $growth,
        ]);
    }

    public function getNewUsers(): JsonResponse
    {
        [$newUsers, $growth] = $this->analyticsService->getNewUsers();

        return response()->json([
            'newUsers' => $newUsers,
            'growth' => $growth,
        ]);
    }

    public function getNewTracks(): JsonResponse
    {
        [$newTracks, $growth] = $this->analyticsService->getNewTracks();

        return response()->json([
            'newTracks' => $newTracks,
            'growth' => $growth,
        ]);
    }

    public function getNewReleases(): JsonResponse
    {
        [$newReleases, $growth] = $this->analyticsService->getNewReleases();

        return response()->json([
            'newReleases' => $newReleases,
            'growth' => $growth,
        ]);
    }

    public function getNewPlaylists(): JsonResponse
    {
        [$newPlaylists, $growth] = $this->analyticsService->getNewPlaylists();

        return response()->json([
            'newPlaylists' => $newPlaylists,
            'growth' => $growth,
        ]);
    }

    public function getMonthPlays(): JsonResponse
    {
        $result = $this->analyticsService->getMonthPlays();

        return response()->json([
            'monthPlays' => $result,
        ]);
    }

    public function getUserGrowth(): JsonResponse
    {
        $result = $this->analyticsService->getUserGrowth();

        return response()->json([
            'userGrowth' => $result
        ]);
    }

    public function getTopArtists(): JsonResponse
    {
        $result = $this->analyticsService->getTopArtists();

        return response()->json([
            'topArtists' => $result
        ]);
    }

    public function getTopReleases(): JsonResponse
    {
        $result = $this->analyticsService->getTopReleases();

        return response()->json([
            'topReleases' => $result
        ]);
    }
}
