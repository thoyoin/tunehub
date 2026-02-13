<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Playlist\GetUserLikedPlaylist;
use App\Actions\Track\DeleteTrack;
use App\Actions\Track\IsTrackLiked;
use getID3;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class TrackService
{
    public function __construct(
        public MinioService $minioService,
        public getID3 $getID3,
        public GetUserLikedPlaylist $getUserLikedPlaylist,
        public IsTrackLiked $isTrackLiked,
        public ReleaseService $releaseService,
        public DeleteTrack $deleteTrack,
    ) {}

    public function destroy($track): void
    {
        DB::transaction(function () use ($track) {
            $this->releaseService->destroy($track);

            $this->minioService->destroyTrack($track);

            $track->delete();
        });
    }

    public function addToLikes($track): JsonResponse
    {
        $isLiked = $this->isTrackLiked->handle($track);

        $likedPlaylist = $this->getUserLikedPlaylist->handle();

        if (! $isLiked) {
            DB::transaction(function () use ($likedPlaylist, $track) {
                DB::table('playlist_track')
                    ->where('playlist_id', $likedPlaylist->id)
                    ->increment('position');

                $likedPlaylist->tracks()->attach($track, [
                    'position' => 1,
                ]);

            });

            return response()->json([
                'likedTrack' => $track->id,
            ]);
        } else {
            DB::transaction(function () use ($likedPlaylist, $track) {
                $currentPosition = DB::table('playlist_track')
                    ->where('playlist_id', $likedPlaylist->id)
                    ->where('track_id', $track->id)
                    ->value('position');

                DB::table('playlist_track')
                    ->where('playlist_id', $likedPlaylist->id)
                    ->where('position', '>', $currentPosition)
                    ->decrement('position');

                DB::table('playlist_track')
                    ->where('playlist_id', $likedPlaylist->id)
                    ->where('track_id', $track->id)
                    ->delete();
            });

            return response()->json([
                'liked' => false,
            ]);
        }
    }

    public function update($track, $request): void
    {
        if ($request->hasFile('cover_url')) {
            $coverUrl = $this->minioService->storeCover($request->file('cover_url'));

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
    }
}
