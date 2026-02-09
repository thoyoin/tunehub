<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class ArtistStudioController extends Controller
{
    public function getTracks(): JsonResponse
    {
        $user = Auth::user();
        $tracks = $user ? $user
            ->tracks()
            ->with('release')
            ->orderBy('created_at', 'desc')
            ->get() : collect();

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
