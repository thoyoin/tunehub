<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PlaylistUpdateRequest;
use App\Models\LibraryItem;
use App\Models\Playlist;
use App\Services\MinioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class PlaylistController extends Controller
{
    public function store(): JsonResponse
    {
        $userId = auth()->id();
        $baseTitle = 'My Playlist';

        $count = Playlist::where('user_id', $userId)
            ->where('title', 'like', $baseTitle.'%')
            ->count();

        $playlist = Playlist::create([
            'title' => 'My Playlist'.' #'.($count + 1),
            'description' => null,
            'user_id' => auth()->id(),
            'cover_url' => 'http://localhost:9000/tunehub/defaults/default_cover.png',
        ]);

        $libraryItem = LibraryItem::create([
            'user_id' => $userId,
            'item_id' => $playlist->id,
            'item_type' => 'playlist',
        ]);

        $newLibraryItem = $libraryItem->with('item')->where('item_id', $playlist->id)->first();

        return response()->json([
            'libraryItem' => $newLibraryItem,
        ]);
    }

    public function show(Playlist $playlist): JsonResponse
    {
        $playlist = $playlist
            ->with(['user', 'tracks.release'])
            ->where('id', $playlist->id)
            ->first();

        $tracks = $playlist
            ->tracks()
            ->orderBy('pivot_position')
            ->with('release')
            ->get();

        $tracks = $tracks->map(function ($track) {
            $track->is_added = true;

            return $track;
        });

        return response()->json([
            'playlist' => $playlist,
            'tracks' => $tracks,
        ]);
    }

    public function destroy(Playlist $playlist, MinioService $minioService): JsonResponse
    {
        Gate::authorize('delete', $playlist);

        $url = $playlist->cover_url;

        $playlist->delete();

        $defaultCover = 'http://localhost:9000/tunehub/defaults/default_cover.png';

        if ($url !== $defaultCover) {
            $minioService->destroyCover($url);
        }

        return response()->json([
            'message' => 'Playlist successfully deleted',
        ]);
    }

    public function update(
        Playlist $playlist,
        PlaylistUpdateRequest $request,
        MinioService $minioService
    ): JsonResponse {
        Gate::authorize('update', $playlist);

        $data = $request->only(['title', 'description', 'cover_url']);

        if ($request->hasFile('cover_url')) {
            $url = $minioService->storeCover($request->file('cover_url'));
            $data['cover_url'] = $url;
        }

        $playlist->update($data);

        return response()->json([
            'message' => 'Successfully updated playlist.',
            'updatedPlaylist' => $playlist,
        ]);
    }
}
