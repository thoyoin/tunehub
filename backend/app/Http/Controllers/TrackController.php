<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TrackStoreRequest;
use App\Http\Requests\TrackUpdateRequest;
use App\Models\Release;
use App\Models\Track;
use App\Services\DestroyTrackService;
use App\Services\ReleaseService;
use App\Services\TrackService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class TrackController
{
    public function store(
        TrackStoreRequest $trackRequest,
        ReleaseService $releaseService,
    ): JsonResponse {
        Gate::authorize('create', Track::class);
        Gate::authorize('create', Release::class);

        $releaseService->store($trackRequest);

        return response()->json([
            'message' => 'Release has been created successfully.',
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
        TrackUpdateRequest $request,
        TrackService $trackService,
    ): JsonResponse {
        Gate::authorize('update', $track);

        $trackService->update($track, $request);

        return response()->json([
            'message' => 'Track has been updated successfully.',
        ]);
    }
}
