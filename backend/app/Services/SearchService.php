<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Playlist;
use App\Models\Release;
use App\Models\Track;
use Illuminate\Http\JsonResponse;

class SearchService
{
    public function index($request): JsonResponse|array
    {
        $query = $request->query('query');

        if (empty($query)) {
            return response()->json();
        }

        return $response = [
            'releases' => Release::where('title', 'LIKE', "%{$query}%")
                ->orwhere('artist', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get(),
            'playlists' => Playlist::where('title', 'LIKE', "%{$query}%")
                ->with('user')
                ->limit(10)
                ->get(),
            'tracks' => Track::where('title', 'LIKE', "%{$query}%")
                ->orwhere('artist', 'LIKE', "%{$query}%")
                ->limit(10)
                ->get(),
        ];
    }
}
