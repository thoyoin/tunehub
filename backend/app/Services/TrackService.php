<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Playlist\AddTrackToPlaylist;
use App\Actions\Playlist\GetUserLikedPlaylist;
use App\Actions\Track\DeleteTrack;
use App\Actions\Track\IsTrackAdded;
use App\Actions\Track\IsTrackLiked;
use getID3;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class TrackService
{
    public function __construct(
        public MinioService $minioService,
        public getID3 $getID3,
        public GetUserLikedPlaylist $getUserLikedPlaylist,
        public IsTrackLiked $isTrackLiked,
        public IsTrackAdded $isTrackAdded,
        public AddTrackToPlaylist $addTrackToPlaylist,
        public ReleaseService $releaseService,
        public DeleteTrack $deleteTrack,
    ) {}

    public function destroy($track): void
    {
        DB::transaction(function () use ($track) {
            $this->releaseService->destroyByTrack($track);

            $this->minioService->destroyTrack($track);

            $track->delete();
        });
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
