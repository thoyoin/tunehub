<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\RecentlyPlayedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecentlyPlayedController
{
    public function store(Request $request, RecentlyPlayedService $recentlyPlayedService): JsonResponse
    {
        $recentlyPlayedService->store($request);

        return response()->json();
    }

    public function get(RecentlyPlayedService $recentlyPlayedService): JsonResponse
    {
        $recentlyPlayed = $recentlyPlayedService->get();

        return response()->json($recentlyPlayed);
    }
}
