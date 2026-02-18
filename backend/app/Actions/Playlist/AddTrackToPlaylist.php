<?php

declare(strict_types=1);

namespace App\Actions\Playlist;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class AddTrackToPlaylist
{
    public function handle($track, $playlist, $isTrackAdded): JsonResponse
    {
        if (! $isTrackAdded) {
            DB::transaction(function () use ($playlist, $track) {
                DB::table('playlist_track')
                    ->where('playlist_id', $playlist->id)
                    ->increment('position');

                $playlist->tracks()->attach($track, [
                    'position' => 1,
                ]);

            });

            return response()->json([
                'likedTrack' => $track->id,
            ]);
        } else {
            DB::transaction(function () use ($playlist, $track) {
                $currentPosition = DB::table('playlist_track')
                    ->where('playlist_id', $playlist->id)
                    ->where('track_id', $track->id)
                    ->value('position');

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
}
