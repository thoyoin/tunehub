<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Release\GetUserReleases;
use App\Actions\Track\GetUserTracks;
use App\Services\TrackStatsService;
use Illuminate\Http\JsonResponse;

class ArtistStudioController extends Controller
{
    public function getTracks(GetUserTracks $getUserTracks, TrackStatsService $trackStatsService): JsonResponse
    {
        $tracks = $getUserTracks->handle();

        $trackStatsService->getTracksPlays($tracks->pluck('id')->toArray(), $tracks);

        return response()->json([
            'tracks' => $tracks,
        ]);
    }

    public function getReleases(GetUserReleases $getUserReleases): JsonResponse
    {
        $releases = $getUserReleases->handle();

        return response()->json([
            'releases' => $releases,
        ]);
    }
}
