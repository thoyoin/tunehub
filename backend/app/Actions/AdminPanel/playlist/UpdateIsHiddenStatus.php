<?php

declare(strict_types=1);

namespace App\Actions\AdminPanel\playlist;

use App\Models\Playlist;

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

        return $playlist->is_hidden;
    }
}
