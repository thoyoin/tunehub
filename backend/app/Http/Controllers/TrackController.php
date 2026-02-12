<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TrackStoreRequest;
use App\Http\Requests\TrackUpdateRequest;
use App\Models\Release;
use App\Models\Track;
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

    public function destroy(Track $track, MinioService $minioService): JsonResponse
    {
        Gate::authorize('delete', $track);

        $minioService->destroyTrack($track);

        $release = $track->release()->withCount('tracks')->first();

        $track->delete();

        if ($release && $release->tracks_count === 1) {
            $release->delete();
        }

        return response()->json([
            'message' => 'Track has been deleted successfully.',
        ]);
    }

    public function addToLikes(Track $track): JsonResponse
    {
        $playlist = auth()
            ->user()
            ->libraryItems()
            ->where('item_type', 'playlist')
            ->with('item')
            ->first()
            ->item;

        $trackIsLiked = $playlist->tracks()->whereKey($track->id)->exists();

        $currentPosition = DB::table('playlist_track')
            ->where('playlist_id', $playlist->id)
            ->where('track_id', $track->id)
            ->value('position');

        if (! $trackIsLiked) {
            DB::transaction(function () use ($playlist) {
                DB::table('playlist_track')
                    ->where('playlist_id', $playlist->id)
                    ->increment('position');
            });

            $playlist->tracks()->attach($track, [
                'position' => 1,
            ]);

            return response()->json([
                'likedTrack' => $track->id,
            ]);
        } else {
            DB::transaction(function () use ($playlist, $track, $currentPosition) {
                DB::table('playlist_track')
                    ->where('playlist_id', $playlist->id)
                    ->where('position', '>', $currentPosition)
                    ->decrement('position');

                DB::table('playlist_track')
                    ->where('playlist_id', $playlist->id)
                    ->where('track_id', $track->id)
                    ->delete();
            });

            return response()->json([
                'liked' => false,
            ]);
        }
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
