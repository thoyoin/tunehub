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
    public function __construct(
        public ReleaseService $releaseService
    )
    {}

    public function show(Release $release): JsonResponse
    {
        [$release, $tracks] = $this->releaseService->get($release);

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

    public function addToLikes(Release $release): JsonResponse
    {
        return $this->releaseService->addToLikes($release);
    }

    public function destroy(Release $release): JsonResponse
    {
        Gate::authorize('delete', $release);

        $this->releaseService->destroy($release);

        return response()->json([
            'message' => 'release deleted successfully',
        ]);
    }

    public function update(
        Release $release,
        ReleaseUpdateRequest $request
    ): JsonResponse {
        Gate::authorize('update', $release);

        $this->releaseService->update($release, $request);

        return response()->json([
            'message' => 'release updated successfully',
        ]);
    }

    public function publish(Release $release): JsonResponse
    {
        Gate::authorize('publish', $release);

        $this->releaseService->publish($release);

        return response()->json([
            'message' => 'release published successfully',
        ]);
    }
}
