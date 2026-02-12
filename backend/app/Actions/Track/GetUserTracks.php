<?php

declare(strict_types=1);

namespace App\Actions\Track;

use Illuminate\Support\Facades\Auth;

class GetUserTracks
{
    public function handle()
    {
        $user = Auth::user();

        return $user ? $user
            ->tracks()
            ->with('release')
            ->orderBy('created_at', 'desc')
            ->get() : collect();
    }
}
