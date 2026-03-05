<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminPanel;

use App\Actions\AdminPanel\playlist\GetAllPlaylists;
use App\Actions\AdminPanel\playlist\UpdateIsHiddenStatus;
use App\Models\Playlist;
use Illuminate\Http\JsonResponse;

class PlaylistController
{
    public function getAll(GetAllPlaylists $getAllPlaylists): JsonResponse
    {
        [$allPlaylists, $privatePlaylists, $hiddenPlaylists] = $getAllPlaylists->handle();

        return response()->json([
            'allPlaylists' => $allPlaylists,
            'privatePlaylists' => $privatePlaylists,
            'hiddenPlaylists' => $hiddenPlaylists,
        ]);
    }

    public function updateStatus(Playlist $playlist, UpdateIsHiddenStatus $updateStatus): JsonResponse
    {
        $status = $updateStatus->handle($playlist);

        return response()->json([
            'message' => 'Playlist is_hidden status updated.',
            'is_hidden' => $status,
        ]);
    }
}
