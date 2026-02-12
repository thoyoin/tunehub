<?php

declare(strict_types=1);

namespace App\Actions\Release;

use Illuminate\Support\Facades\Auth;

class GetUserReleases
{
    public function handle()
    {
        $user = Auth::user();

        return $user ? $user
            ->releases()
            ->with('tracks')
            ->orderBy('created_at', 'desc')
            ->get() : collect();
    }
}
