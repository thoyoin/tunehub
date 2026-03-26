<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\User;
use App\Services\ArtistService;
use App\Services\ReleaseService;
use App\Services\TrackService;
use Illuminate\Http\JsonResponse;

class ArtistController extends Controller
{
    public function getArtist(User $artist, ArtistService $artistService): JsonResponse
    {
        $artist = $artistService->getArtist($artist->id);

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

    public function getAlbums(User $artist, ArtistService $artistService): JsonResponse
    {
        $albums = $artistService->getAlbums($artist->id);

        return response()->json([
            'albums' => $albums,
        ]);
    }
}
