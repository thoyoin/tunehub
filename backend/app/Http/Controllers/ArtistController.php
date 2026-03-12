<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ReleaseService;
use App\Services\TrackService;
use Illuminate\Http\JsonResponse;

class ArtistController extends Controller
{
    public function getArtist(User $artist): JsonResponse
    {
        return response()->json([
            'artist' => $artist,
        ]);
    }

    public function getLatestRelease(User $artist, ReleaseService $releaseService): JsonResponse
    {
        $release = $releaseService->getArtistLatest($artist->id);

        return response()->json([
            'latestRelease' => $release,
        ]);
    }

    public function getTopTracks(User $artist, TrackService $trackService): JsonResponse
    {
        $tracks = $trackService->getArtistTopTracks($artist->id);

        return response()->json([
            'topTracks' => $tracks,
        ]);
    }
}
