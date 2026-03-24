<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Artist\GetArtistStreams;
use App\Actions\Release\GetUserReleases;
use App\Actions\Track\GetUserTracks;
use App\Models\Product;
use App\Models\ProductImage;
use App\Models\ProductVariant;
use App\Models\Release;
use App\Models\Track;
use ClickHouseDB\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class ArtistStudioService
{
    public function __construct(
        public Client $clickhouse,
        public GetArtistStreams $getArtistStreams,
        public GetUserTracks $getUserTracks,
        public GetUserReleases $getUserReleases,
        public MinioService $minioService
    )
    {}

    public function getArtistStats()
    {
        return $this->getArtistStreams->handle();
    }

    public function getArtistEarnings()
    {
        $artistId = Auth::id();

        $rows = $this->clickhouse->select("
            SELECT
                formatDateTime(date, '%b %d') as date,
                earnings
            FROM
                artist_earnings_daily
            WHERE
                artist_id = $artistId
            ORDER BY
                date ASC
        ")->rows();

        $date = array_column($rows, 'date');
        $earnings = array_column($rows, 'earnings');

        $totalEarnings = array_sum($earnings);

        return [
            'date' => $date,
            'earnings' => $earnings,
            'total_earnings' => $totalEarnings,
        ];
    }

    public function getDailyStreams(): array
    {
        $artistId = Auth::id();

        return $this->clickhouse->select("
            SELECT
                formatDateTime(date, '%b %d, %Y') as date,
                plays
                FROM
                    artist_earnings_daily
            WHERE
                artist_id = :artist_id
            ORDER BY
                date ASC
        ",['artist_id' => $artistId])->rows();
    }

    public function getTopTracks()
    {
        $artistId = Auth::id();

        $rows = $this->clickhouse->select("
            SELECT
                track_id,
                sum(plays) as streams
                FROM
                    track_plays_total
            WHERE
                track_artist_id = :artist_id
            GROUP BY
                track_id
            ORDER BY
                streams DESC
            LIMIT 5
        ", ['artist_id' => $artistId])->rows();

        $tracks = Track::whereIn('id', array_column($rows, 'track_id'))
            ->get()
            ->keyBy('id');

        return collect($rows)->map(function ($row) use ($tracks) {
            $track = $tracks[$row['track_id']];

            return [
                'track' => $track,
                'streams' => $row['streams'],
            ];
        });
    }

    public function getTopReleases()
    {
        $artistId = Auth::id();

        $rows = $this->clickhouse->select("
            SELECT
                release_id,
                sum(plays) as streams
                FROM
                    release_streams
            WHERE
                release_artist_id = :artist_id
            GROUP BY
                release_id
            ORDER BY
                streams DESC
        ", ['artist_id' => $artistId])->rows();

        $releases = Release::where('user_id', $artistId)
            ->whereIn('id', array_column($rows, 'release_id'))
            ->get()
            ->keyBy('id');

        foreach($rows as $row){
            $releases[$row['release_id']]->plays = $row['streams'];
        }

        return collect(array_column($rows, 'release_id'))
            ->map(fn ($id) => $releases[$id])
            ->values();
    }

    public function dropMerch(Request $request): void
    {
        $itemTitle = $request->input('item_title');
        $itemDescription = $request->input('item_description');
        $merchVariants = json_decode($request->input('merch_variants'), true);
        $userId = auth()->id();

        DB::transaction(function () use (
            $userId,
            $itemTitle,
            $itemDescription,
            $merchVariants,
            $request
        ) {
            $coverPaths = [];

            if($request->hasFile('images')) {
                foreach($request->file('images') as $image){
                    $coverPaths[] = $this->minioService->storeMerchCover($image);
                }
            }

            $product = Product::create([
                'user_id' => $userId,
                'title' => $itemTitle,
                'description' => $itemDescription,
                'cover_url' => $coverPaths[0] ?? null,
                'currency' => 'usd',
                'status' => 'pending',
            ]);

            $product->productImages()->createMany(
                array_map(fn($cover) => ['image_url' => $cover], $coverPaths)
            );

            foreach($merchVariants as $variant){
                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $variant['variant'],
                    'price' => $variant['price'],
                    'stock' => $variant['stock'],
                ]);
            }
        });
    }

    public function getMerch(): Collection
    {
        $artistId = Auth::id();

        return Product::where('user_id', $artistId)
            ->with(['productVariants', 'productImages'])
            ->get();
    }

    public function updateMerch(Request $request, Product $merch): void
    {
        $existingImages = json_decode($request->input('existing_images'), true) ?? [];
        $existingImageUrls = array_column($existingImages, 'image_url');
        $merchVariants = json_decode($request->input('merch_variants'), true);

        DB::transaction(function () use (
            $request,
            $merch,
            $existingImageUrls,
            $merchVariants,
        ) {
            $imagesToDelete = $merch->productImages()
                ->whereNotIn('image_url', $existingImageUrls)
                ->get();

            foreach ($imagesToDelete as $image) {
                $this->minioService->destroyCover($image->image_url);
                $image->delete();
            }

            $newImagePaths = [];

            if ($request->hasFile('new_images')) {
                foreach ($request->file('new_images') as $image) {
                    $path = $this->minioService->storeMerchCover($image);
                    $newImagePaths[] = $path;
                }

                $merch->productImages()->createMany(
                    array_map(fn ($path) => ['image_url' => $path], $newImagePaths)
                );
            }

            $finalImages = array_merge($existingImageUrls, $newImagePaths);

            $merch->update([
                'title' => $request->input('item_title'),
                'description' => $request->input('item_description'),
                'cover_url' => $finalImages[0] ?? null,
            ]);

            $variantIds = collect($merchVariants)
                ->pluck('id')
                ->filter()
                ->values()
                ->all();

            $merch->productVariants()
                ->whereNotIn('id', $variantIds)
                ->delete();

            foreach($merchVariants as $variant){
                $merch->productVariants()->updateOrCreate(
                    [
                        'id' => $variant['id'] ?? null,
                        ],
                    [
                        'variant_name' => $variant['variant_name'],
                        'price' => $variant['price'],
                        'stock' => $variant['stock'],
                    ]
                );
            }
        });
    }
}
