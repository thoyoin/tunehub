<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminPanel;

use App\Services\AnalyticsService;
use Illuminate\Http\JsonResponse;

class AnalyticsController
{
    public function getTotalPlays(AnalyticsService $analytics): JsonResponse
    {
        $totalPlays = $analytics->getTotalPlays();

        return response()->json([
            'totalPlays' => $totalPlays,
        ]);
    }

    public function getNewUsers(AnalyticsService $analytics): JsonResponse
    {
        $newUsers = $analytics->getNewUsers();

        return response()->json([
            'newUsers' => $newUsers,
        ]);
    }
}
