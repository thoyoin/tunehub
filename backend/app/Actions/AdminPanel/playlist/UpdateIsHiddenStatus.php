<?php

declare(strict_types=1);

namespace App\Actions\AdminPanel\playlist;

use App\Models\Playlist;
use Illuminate\Support\Facades\Log;

class UpdateIsHiddenStatus
{
    public function handle(Playlist $playlist)
    {
        if (!$playlist->is_hidden) {
            $playlist->is_hidden = true;
        } else {
            $playlist->is_hidden = false;
        }

        $playlist->save();

        Log::info('Moderation: playlist status was updated', [
            'playlist' => $playlist->title,
            'is_hidden' => $playlist->is_hidden,
        ]);

        return $playlist->is_hidden;
    }
}
