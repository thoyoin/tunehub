<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminPanel;

use App\Actions\Release\GetReleases;
use App\Actions\Release\UpdateStatus;
use App\Models\Release;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;

class ReleaseController
{
    public function getReleases(GetReleases $getReleases, Request $request): JsonResponse
    {
        $releases = $getReleases->handle($request);

        return response()->json($releases);
    }

    public function updateStatus(Request $request, UpdateStatus $updateStatus, Release $release): JsonResponse
    {
        Gate::authorize('updateStatus', Release::class);

        $updateStatus->handle($request, $release);

        return response()->json([
            'message' => 'Status updated successfully!'
        ]);
    }
}
