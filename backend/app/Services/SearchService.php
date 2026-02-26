<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Playlist;
use App\Models\Release;
use App\Models\Track;
use App\Models\User;
use Illuminate\Http\JsonResponse;

class SearchService
{
    public function getContent($request): JsonResponse|array
    {
        $query = $request->query('query');

        if (empty($query)) {
            return response()->json();
        }

        return $response = [
            'releases' => Release::where('status', 'published')
                ->where('title', 'LIKE', "%{$query}%")
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

    public function getUsers($request): JsonResponse|array
    {
        $query = $request->query('query');

        if (empty($query)) {
            return response()->json();
        }

        return $response = [
            'users' => User::where('username', 'LIKE', "%{$query}%")
            ->orwhere('email', 'LIKE', "%{$query}%")
            ->paginate(10)
        ];
    }
}
