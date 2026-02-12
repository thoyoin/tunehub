<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Release\GetUserReleases;
use App\Actions\Track\GetUserTracks;
use Illuminate\Http\JsonResponse;

class ArtistStudioController extends Controller
{
    public function getTracks(GetUserTracks $getUserTracks): JsonResponse
    {
        $tracks = $getUserTracks->handle();

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
