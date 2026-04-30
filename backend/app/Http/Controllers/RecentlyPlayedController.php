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
        $data = $request->validate([
            'track_id' => ['required', 'integer'],
            'track_artist_id' => ['required', 'integer'],
            'release_id' => ['required', 'integer'],
        ]);

        $this->recentlyPlayedService->store($data);

        return response()->json();
    }

    public function get(): JsonResponse
    {
        $recentlyPlayed = $this->recentlyPlayedService->get();

        return response()->json([
            'playedHistory' => $recentlyPlayed
        ]);
    }
}
