<?php

namespace App\Http\Controllers;

use App\Http\Requests\ReleaseUpdateRequest;
use App\Models\LibraryItem;
use App\Models\Release;
use App\Services\MinioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Gate;

class ReleaseController extends Controller
{
    public function show(Release $release): JsonResponse
    {
        $isReleaseLiked = false;

        if (auth()->check()) {
            $playlist = auth()
                ->user()
                ->playlists()
                ->where('slug', 'liked-tracks')
                ->first();

            $release->tracks->map(function ($track) use ($playlist) {
                return $track->is_added = (bool) $playlist->tracks->contains($track->id);
            });

            $isReleaseLiked = auth()
                ->user()
                ->libraryItems()
                ->where('item_type', 'release')
                ->where('item_id', $release->id)
                ->exists();
        } else {
            $release->tracks;
        }

        return response()->json([
            'release' => $release,
            'isReleaseLiked' => $isReleaseLiked,
        ]);
    }

    public function getLatest(): JsonResponse
    {
        $releases = Release::with('user')
            ->orderBy('release_date', 'desc')
            ->limit(10)
            ->get();

        return response()->json($releases);
    }

    public function addToLikes(Release $release): JsonResponse
    {
        $libraryItem = auth()
            ->user()
            ->libraryItems()
            ->where('item_type', 'release')
            ->where('item_id', $release->id)
            ->first();

        if (! $libraryItem) {
            LibraryItem::create([
                'user_id' => auth()->id(),
                'item_type' => 'release',
                'item_id' => $release->id,
            ]);

            return response()->json([
                'liked' => true,
            ]);
        } else {
            $libraryItem->delete();

            return response()->json([
                'liked' => false,
            ]);
        }
    }

    public function destroy(Release $release, MinioService $minioService): JsonResponse
    {
        Gate::authorize('delete', $release);

        $releaseTracks = $release->tracks()->get();

        $release->delete();

        foreach ($releaseTracks as $track) {
            $minioService->destroyTrack($track);
        }

        return response()->json([
            'message' => 'Release deleted successfully',
        ]);
    }

    public function update(Release $release, MinioService $minioService, ReleaseUpdateRequest $request)
    {
        Gate::authorize('update', $release);

        if ($request->hasFile('cover_url')) {
            $coverUrl = $minioService->storeCover($request->file('cover_url'));

            $release->update([
                'title' => $request['releaseTitle'],
                'artist' => $request['artist'],
                'cover_url' => $coverUrl,
            ]);
        } else {
            $release->update([
                'title' => $request['releaseTitle'],
                'artist' => $request['artist'],
            ]);
        }

        return response()->json([
            'message' => 'Release updated successfully',
        ]);
    }
}
