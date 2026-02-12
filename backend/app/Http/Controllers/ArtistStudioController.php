<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Track\GetUserTracks;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ArtistStudioController extends Controller
{
    public function getTracks(GetUserTracks $getUserTracks): JsonResponse
    {
        $tracks = $getUserTracks->handle();

        return response()->json([
            'tracks' => $tracks,
        ]);
    }

    public function getReleases(): JsonResponse
    {
        $user = Auth::user();

        $releases = $user ? $user
            ->releases()
            ->with('tracks')
            ->orderBy('created_at', 'desc')
            ->get() : collect();

        return response()->json([
            'releases' => $releases,
        ]);
    }
}
