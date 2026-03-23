<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Release\GetUserReleases;
use App\Actions\Track\GetUserTracks;
use App\Services\ArtistStudioService;
use App\Services\TrackStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function getStreams(ArtistStudioService $artistStudioService): JsonResponse
    {
        $streams = $artistStudioService->getArtistStats();

        return response()->json([
            'artistStreams' => $streams,
        ]);
    }

    public function getEarnings(ArtistStudioService $artistStudioService): JsonResponse
    {
        $earnings = $artistStudioService->getArtistEarnings();

        return response()->json([
            'earnings' => $earnings,
        ]);
    }

    public function getDailyStreams(ArtistStudioService $artistStudioService): JsonResponse
    {
        $streams = $artistStudioService->getDailyStreams();

        return response()->json([
            'streamsDaily' => $streams,
        ]);
    }

    public function getTopTracks(ArtistStudioService $artistStudioService): JsonResponse
    {
        $tracks = $artistStudioService->getTopTracks();

        return response()->json($tracks);
    }

    public function getTopReleases(ArtistStudioService $artistStudioService): JsonResponse
    {
        $releases = $artistStudioService->getTopReleases();

        return response()->json($releases);
    }

    public function dropMerch(ArtistStudioService $artistStudioService, Request $request): JsonResponse
    {
        $artistStudioService->dropMerch($request);

        return response()->json([
            'message' => 'Merch has been successfully uploaded.',
        ]);
    }

    public function getMerch(ArtistStudioService $artistStudioService): JsonResponse
    {
        $merch = $artistStudioService->getMerch();

        return response()->json([
            'merch' => $merch,
        ]);
    }
}
