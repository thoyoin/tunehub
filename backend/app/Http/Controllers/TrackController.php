<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\TrackStoreRequest;
use App\Http\Requests\TrackUpdateRequest;
use App\Models\Release;
use App\Models\Track;
use App\Services\MinioService;
use getID3;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrackController
{
    public GetID3 $getID3;

    public function __construct(getID3 $getID3)
    {
        $this->getID3 = $getID3;
    }

    public function store(
        TrackStoreRequest $trackRequest,
        MinioService $minioService
    ): JsonResponse {
        $releaseData = $trackRequest->only(['albumTitle', 'artist', 'type', 'cover_url', 'release_date']);

        $coverUrl = $minioService->storeCover($releaseData['cover_url']);

        $release = auth()
            ->user()
            ->releases()
            ->create([
                'title' => $releaseData['albumTitle'] ?? $trackRequest['title'],
                'type' => $releaseData['type'],
                'artist' => $releaseData['artist'],
                'cover_url' => $coverUrl,
                'release_date' => $releaseData['release_date'],
            ]);

        $files = $trackRequest->file('audio_url');
        $titles = $trackRequest->input('title');

        foreach ($files as $index => $file) {
            $title = $titles[$index];
            $fileInfo = $this->getID3->analyze($file->getPathname());
            $duration = round($fileInfo['playtime_seconds']);
            $audioUrl = $minioService->storeTrack($file);

            $release->tracks()->create([
                'title' => $title,
                'artist' => $release->artist,
                'user_id' => Auth::id(),
                'release_date' => $release->release_date,
                'audio_url' => $audioUrl,
                'cover_url' => $release->cover_url,
                'duration' => $duration,
                'position' => $index + 1,
            ]);
        }

        return response()->json([
            'message' => 'Track has been created successfully.',
            'release' => $release,
        ]);
    }

    public function destroy(Track $track, MinioService $minioService): JsonResponse
    {
        $minioService->destroyTrack($track);

        $track->delete();

        if (Release::all()->find($track->release_id)->tracks->count() <= 1) {
            Release::all()->find($track->release_id)->delete();
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

        $playlistTracks = $playlist->tracks;

        $currentPosition = DB::table('playlist_track')
            ->where('playlist_id', $playlist->id)
            ->where('track_id', $track->id)
            ->value('position');

        if (! $playlistTracks->contains($track)) {
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
