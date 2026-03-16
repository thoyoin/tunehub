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
        $data = $request->only('id', 'item_type', 'track_id', 'track_artist_id', 'release_id');

        $response = $recentlyPlayedService->store($data);

        return response()->json($response);
    }

    public function get(RecentlyPlayedService $recentlyPlayedService): JsonResponse
    {
        $recentlyPlayed = $recentlyPlayedService->get();

        return response()->json([
            'playedHistory' => $recentlyPlayed
        ]);
    }
}
