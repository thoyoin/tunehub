<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Playlist\AddTrackToPlaylist;
use App\Actions\Playlist\GetUserLikedPlaylist;
use App\Actions\Track\DeleteTrack;
use App\Actions\Track\IsTrackAdded;
use App\Actions\Track\IsTrackLiked;
use App\Jobs\DeleteTrackAudioFile;
use App\Jobs\DeleteTrackInClickhouse;
use ClickHouseDB\Client;
use getID3;
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
        public Client $clickhouse,
    ) {}

    public function destroy($track): void
    {
        DB::transaction(function () use ($track) {
            $audioPath = $track->audio_url;

            $this->releaseService->destroyByTrack($track);

            $track->delete();

            DB::afterCommit(function () use ($audioPath, $track) {
                DeleteTrackAudioFile::dispatch($audioPath);
                DeleteTrackInClickhouse::dispatch($track->id);
            });
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
