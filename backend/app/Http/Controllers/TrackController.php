<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Playlist\GetUserLikedPlaylist;
use App\Http\Requests\TrackStoreRequest;
use App\Http\Requests\TrackUpdateRequest;
use App\Models\Release;
use App\Models\Track;
use App\Services\DestroyTrackService;
use App\Services\MinioService;
use App\Services\ReleaseService;
use App\Services\TrackService;
use App\Services\UploadReleaseService;
use getID3;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Gate;

class TrackController
{
    public function store(
        TrackStoreRequest $trackRequest,
        UploadReleaseService $uploadReleaseService,
    ): JsonResponse {
        Gate::authorize('create', Track::class);
        Gate::authorize('create', Release::class);

        $release = $uploadReleaseService->handle($trackRequest);

        return response()->json([
            'message' => 'Release has been created successfully.',
            'release' => $release,
        ]);
    }

    public function destroy(
        Track $track,
        DestroyTrackService $destroyTrackService,
    ): JsonResponse {
        Gate::authorize('delete', $track);

        $destroyTrackService->handle($track);

        return response()->json([
            'message' => 'Track has been deleted successfully.',
        ]);
    }

    public function addToLikes(
        Track $track,
        TrackService $trackService,
    ): JsonResponse {
        $response = $trackService->addToLikes($track);

        return response()->json($response);
    }

    public function update(
        Track $track,
        MinioService $minioService,
        TrackUpdateRequest $request
    ): JsonResponse {
        Gate::authorize('update', $track);

        if ($request->hasFile('cover_url')) {
            $coverUrl = $minioService->storeCover($request->file('cover_url'));

            $track->update([
                'title' => $request['trackTitle'],
                'artist' => $request['artist'],
                'cover_url' => $coverUrl,
            ]);
        } else {
            $track->update([
                'title' => $request['trackTitle'],
                'artist' => $request['artist'],
            ]);
        }

        return response()->json([
            'message' => 'Track has been updated successfully.',
        ]);
    }
}
