<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PlaylistUpdateRequest;
use App\Models\Playlist;
use App\Models\Track;
use App\Services\PlaylistService;
use App\Services\TrackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class PlaylistController extends Controller
{
    public function store(PlaylistService $playlistService): JsonResponse
    {
        $newLibraryItem = $playlistService->store();

        return response()->json([
            'libraryItem' => $newLibraryItem,
        ]);
    }

    public function show(Playlist $playlist, PlaylistService $playlistService): JsonResponse
    {
        [$playlistItem, $tracks] = $playlistService->get($playlist);

        return response()->json([
            'playlistItem' => $playlistItem,
            'tracks' => $tracks,
        ]);
    }

    public function destroy(Playlist $playlist, PlaylistService $playlistService): JsonResponse
    {
        Gate::authorize('delete', $playlist);

        $playlistService->delete($playlist);

        return response()->json([
            'message' => 'playlist successfully deleted',
        ]);
    }

    public function update(
        Playlist $playlist,
        PlaylistUpdateRequest $request,
        PlaylistService $playlistService
    ): JsonResponse {
        Gate::authorize('update', $playlist);

        $playlist = $playlistService->update($request, $playlist);

        return response()->json([
            'message' => 'Successfully updated playlist.',
            'playlist' => $playlist,
        ]);
    }

    public function updateVisibility(
        Playlist $playlist,
        Request $request,
        PlaylistService $playlistService
    ): JsonResponse {
        $visibility = $playlistService->updateVisibility($playlist, $request);

        return response()->json([
            'message' => 'Successfully updated playlist.',
            'visibility' => $visibility,
        ]);
    }

    public function getAll(PlaylistService $playlistService): JsonResponse
    {
        $playlists = $playlistService->getAll();

        return response()->json([
            'playlists' => $playlists,
        ]);
    }

    public function addTrack(Playlist $playlist, Track $track, PlaylistService $playlistService): JsonResponse
    {
        $response = $playlistService->addTrack($playlist, $track);

        return response()->json($response);
    }

    public function addTrackToLikes(Track $track, PlaylistService $playlistService): JsonResponse
    {
        $response = $playlistService->addTrackToLikes($track);

        return response()->json($response);
    }
}
