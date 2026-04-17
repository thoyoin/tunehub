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
            'id' => ['required', 'integer'],
            'item_type' => ['required', 'string', 'in:playlist,release'],
            'track_id' => ['required', 'integer'],
            'track_artist_id' => ['required', 'integer'],
            'release_id' => ['required', 'integer'],
        ]);

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
