<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\RecentlyPlayedService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class RecentlyPlayedController
{
    public function __construct(
        public RecentlyPlayedService $recentlyPlayedService
    )
    {}

    public function store(Request $request): JsonResponse
    {
        $data = $request->only('id', 'item_type', 'track_id', 'track_artist_id', 'release_id');

        $response = $this->recentlyPlayedService->store($data);

        return response()->json($response);
    }

    public function get(): JsonResponse
    {
        $recentlyPlayed = $this->recentlyPlayedService->get();

        return response()->json([
            'playedHistory' => $recentlyPlayed
        ]);
    }
}
