<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\PlaylistUpdateRequest;
use App\Models\Playlist;
use App\Services\MinioService;
use App\Services\PlaylistService;
use Illuminate\Http\JsonResponse;
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
        [$playlist, $tracks] = $playlistService->get($playlist);

        return response()->json([
            'playlist' => $playlist,
            'tracks' => $tracks,
        ]);
    }

    public function destroy(Playlist $playlist, PlaylistService $playlistService): JsonResponse
    {
        Gate::authorize('delete', $playlist);

        $playlistService->delete($playlist);

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
            'playlist' => $playlist,
        ]);
    }
}
