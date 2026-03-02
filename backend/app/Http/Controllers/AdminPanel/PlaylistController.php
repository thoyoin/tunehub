<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminPanel;

use App\Actions\AdminPanel\playlist\GetAllPlaylists;
use Illuminate\Http\JsonResponse;

class PlaylistController
{
    public function getAll(GetAllPlaylists $getAllPlaylists): JsonResponse
    {
        $playlists = $getAllPlaylists->handle();

        return response()->json($playlists);
    }
}
