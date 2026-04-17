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

        return [
            'releases' => Release::where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('artist', 'LIKE', "%{$query}%");
            })
                ->where('status', 'published')
                ->limit(10)
                ->get(),
            'playlists' => Playlist::where('title', 'LIKE', "%{$query}%")
                ->where('visibility', 'public')
                ->with('user')
                ->limit(10)
                ->get(),
            'tracks' => Track::where(function ($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                    ->orWhere('artist', 'LIKE', "%{$query}%");
            })
                ->whereHas('release', function ($q) {
                    $q->where('status', 'published');
                })
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

        return [
            'users' => User::query()
                ->select(['id', 'username', 'profile_picture'])
                ->where('username', 'LIKE', "%{$query}%")
                ->paginate(10),
        ];
    }
}
