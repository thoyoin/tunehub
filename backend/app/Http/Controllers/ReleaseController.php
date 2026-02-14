<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Release\GetLatestReleases;
use App\Http\Requests\ReleaseUpdateRequest;
use App\Models\Release;
use App\Services\ReleaseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ReleaseController extends Controller
{
    public function show(Release $release, ReleaseService $releaseService): JsonResponse
    {
        [$release, $tracks] = $releaseService->get($release);

        return response()->json([
            'release' => $release,
            'tracks' => $tracks,
        ]);
    }

    public function getLatest(GetLatestReleases $getLatestReleases): JsonResponse
    {
        $releases = $getLatestReleases->handle();

        return response()->json($releases);
    }

    public function addToLikes(Release $release, ReleaseService $releaseService): JsonResponse
    {
        return $releaseService->addToLikes($release);
    }

    public function destroy(Release $release, ReleaseService $releaseService): JsonResponse
    {
        Gate::authorize('delete', $release);

        $releaseService->destroy($release);

        return response()->json([
            'message' => 'release deleted successfully',
        ]);
    }

    public function update(
        Release $release,
        ReleaseService $releaseService,
        ReleaseUpdateRequest $request
    ): JsonResponse {
        Gate::authorize('update', $release);

        $releaseService->update($release, $request);

        return response()->json([
            'message' => 'release updated successfully',
        ]);
    }
}
