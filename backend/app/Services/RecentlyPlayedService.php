<?php

declare(strict_types=1);

namespace App\Services;

use App\Events\TrackListened;
use App\Models\RecentlyPlayed;
use Illuminate\Http\JsonResponse;

class RecentlyPlayedService
{
    public function store($data): JsonResponse
    {
        TrackListened::dispatch($data['user_id'], $data['id'], $data['item_type']);

        return response()->json([
            'sentData' => $data,
        ]);
    }

    public function get()
    {
         return RecentlyPlayed::where('user_id', auth()->id())
             ->with('item.tracks')
             ->orderBy('played_at', 'desc')
             ->get();
    }
}
