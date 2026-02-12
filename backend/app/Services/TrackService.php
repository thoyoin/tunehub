<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Playlist\GetUserLikedPlaylist;
use App\Models\Track;
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
    ) {}

    public function store(
        $files,
        $titles,
        $release,
    ): void {
        foreach ($files as $index => $file) {
            $title = $titles[$index];
            $fileInfo = $this->getID3->analyze($file->getPathname());

            $duration = round($fileInfo['playtime_seconds']);
            $audioUrl = $this->minioService->storeTrack($file);

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
    }

    public function destroy($track): void
    {
        $this->minioService->destroyTrack($track);

        $track->delete();
    }

    public function addToLikes($track): JsonResponse
    {
        $isLiked = $this->isTrackLiked($track);

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

    public function isTrackLiked(Track $track): bool
    {
        $likedPlaylist = $this->getUserLikedPlaylist->handle();

        return $likedPlaylist->tracks()->whereKey($track->id)->exists();
    }
}
