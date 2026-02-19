<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\RecentlyPlayed;

class RecentlyPlayedService
{
    public function store($request): void
    {
        RecentlyPlayed::updateOrCreate(
            [
                'user_id' => auth()->id(),
                'item_type' => $request->item_type,
                'item_id' => $request->id,
            ],
            [
                'played_at' => now(),
            ]
        );

//        RecentlyPlayed::where('user_id', auth()->id())
//            ->orderByDesc('played_at')
//            ->skip(20)
//            ->take(PHP_INT_MAX)
//            ->delete();
    }

    public function get()
    {
         return RecentlyPlayed::where('user_id', auth()->id())
             ->with('item.tracks')
             ->orderBy('played_at', 'desc')
             ->get();
    }
}
