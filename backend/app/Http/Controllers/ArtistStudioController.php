<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Release\GetUserReleases;
use App\Actions\Track\GetUserTracks;
use App\Models\Product;
use App\Services\ArtistStudioService;
use App\Services\TrackStatsService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ArtistStudioController extends Controller
{
    public function __construct(
        public ArtistStudioService $artistStudioService,
    )
    {}

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

    public function getStreams(): JsonResponse
    {
        $streams = $this->artistStudioService->getArtistStats();

        return response()->json([
            'artistStreams' => $streams,
        ]);
    }

    public function getEarnings(): JsonResponse
    {
        $earnings = $this->artistStudioService->getArtistEarnings();

        return response()->json([
            'earnings' => $earnings,
        ]);
    }

    public function getDailyStreams(): JsonResponse
    {
        $streams = $this->artistStudioService->getDailyStreams();

        return response()->json([
            'streamsDaily' => $streams,
        ]);
    }

    public function getTopTracks(): JsonResponse
    {
        $tracks = $this->artistStudioService->getTopTracks();

        return response()->json($tracks);
    }

    public function getTopReleases(): JsonResponse
    {
        $releases = $this->artistStudioService->getTopReleases();

        return response()->json($releases);
    }

    public function dropMerch(Request $request): JsonResponse
    {
        Gate::authorize('create', Product::class);

        $this->artistStudioService->dropMerch($request);

        return response()->json([
            'message' => 'Merch has been successfully uploaded.',
        ], 201);
    }

    public function getMerch(): JsonResponse
    {
        $merch = $this->artistStudioService->getMerch();

        return response()->json([
            'merch' => $merch,
        ]);
    }

    public function updateMerch(Product $merch, Request $request): JsonResponse
    {
        Gate::authorize('update', Product::class);

        $this->artistStudioService->updateMerch($request, $merch);

        return response()->json([
            'message' => 'Merch has been successfully updated.',
        ]);
    }

    public function deleteMerch(Product $merch): JsonResponse
    {
        Gate::authorize('delete', Product::class);

        $this->artistStudioService->deleteMerch($merch->id);

        return response()->json([
            'message' => 'Merch has been successfully deleted.',
        ]);
    }

    public function publishMerch(Product $merch): JsonResponse
    {
        Gate::authorize('publish', $merch);

        $this->artistStudioService->publishMerch($merch);

        return response()->json([
            'message' => 'Merch has been successfully published.',
        ]);
    }
}
