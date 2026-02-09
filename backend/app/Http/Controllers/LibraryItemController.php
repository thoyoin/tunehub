<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\LibraryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LibraryItemController extends Controller
{
    public function show(LibraryItem $id)
    {
        $libraryItem = $id
            ->with('item')
            ->with('user')
            ->where('id', $id->id)
            ->first();

        $isRelease = $libraryItem->item_type == 'release';

        $itemTracks = $libraryItem
            ->item()
            ->with('tracks.release')
            ->get();

        return response()->json([
            'libraryItem' => $libraryItem,
            'itemTracks' => $itemTracks,
            'isRelease' => $isRelease,
        ]);
    }

    public function getAll(): JsonResponse
    {
        $user = Auth::user();

        $libraryItems = $user
            ->libraryItems()
            ->with('user')
            ->with('item.tracks')
            ->get();

        return response()->json([
            'libraryItems' => $libraryItems,
        ]);
    }
}
