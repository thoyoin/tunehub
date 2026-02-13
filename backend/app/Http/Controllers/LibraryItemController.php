<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\LibraryItem\GetAllUserLibraryItems;
use App\Actions\LibraryItem\GetItemTracks;
use App\Actions\LibraryItem\GetUserLibraryItem;
use App\Actions\LibraryItem\IsItemRelease;
use App\Models\LibraryItem;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class LibraryItemController extends Controller
{
    public function show(
        LibraryItem $id,
        GetUserLibraryItem $getUserLibraryItem,
        IsItemRelease $isItemRelease,
        GetItemTracks $getItemTracks,
    ): JsonResponse {
        $libraryItem = $getUserLibraryItem->handle($id);

        $isRelease = $isItemRelease->handle($libraryItem);

        $itemTracks = $getItemTracks->handle($libraryItem);

        return response()->json([
            'libraryItem' => $libraryItem,
            'itemTracks' => $itemTracks,
            'isRelease' => $isRelease,
        ]);
    }

    public function getAll(GetAllUserLibraryItems $getAllUserLibraryItems): JsonResponse
    {
        $libraryItems = $getAllUserLibraryItems->handle();

        return response()->json([
            'libraryItems' => $libraryItems,
        ]);
    }
}
