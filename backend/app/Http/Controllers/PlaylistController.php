<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PlaylistUpdateRequest;
use App\Models\Playlist;
use App\Models\Track;
use App\Services\PlaylistService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PlaylistController extends Controller
{
    public function __construct(
        public PlaylistService $playlistService,
    )
    {}

    public function store(): JsonResponse
    {
        $newLibraryItem = $this->playlistService->store();

        return response()->json([
            'libraryItem' => $newLibraryItem,
        ]);
    }

    public function show(Playlist $playlist): JsonResponse
    {
        Gate::authorize('view', $playlist);

        [$playlistItem, $tracks] = $this->playlistService->get($playlist);

        return response()->json([
            'playlistItem' => $playlistItem,
            'tracks' => $tracks,
        ]);
    }

    public function destroy(Playlist $playlist): JsonResponse
    {
        Gate::authorize('delete', $playlist);

        $this->playlistService->delete($playlist);

        return response()->json([
            'message' => 'playlist successfully deleted',
        ]);
    }

    public function update(
        Playlist $playlist,
        PlaylistUpdateRequest $request,
    ): JsonResponse {
        Gate::authorize('update', $playlist);

        $playlist = $this->playlistService->update($request, $playlist);

        return response()->json([
            'message' => 'Successfully updated playlist.',
            'playlist' => $playlist,
        ]);
    }

    public function updateVisibility(
        Playlist $playlist,
        Request $request,
    ): JsonResponse {
        Gate::authorize('update', $playlist);

        $visibility = $this->playlistService->updateVisibility($playlist, $request);

        return response()->json([
            'message' => 'Successfully updated playlist.',
            'visibility' => $visibility,
        ]);
    }

    public function getAll(): JsonResponse
    {
        $playlists = $this->playlistService->getAll();

        return response()->json([
            'playlists' => $playlists,
        ]);
    }

    public function addTrack(Playlist $playlist, Track $track): JsonResponse
    {
        Gate::authorize('update', $playlist);

        $response = $this->playlistService->addTrack($playlist, $track);

        return response()->json($response);
    }

    public function addTrackToLikes(Track $track): JsonResponse
    {
        $response = $this->playlistService->addTrackToLikes($track);

        return response()->json($response);
    }
}
