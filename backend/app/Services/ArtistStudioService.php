<?php

declare(strict_types=1);

namespace App\Services;

use App\Actions\Artist\GetArtistStreams;
use App\Actions\Release\GetUserReleases;
use App\Actions\Track\GetUserTracks;
use App\Jobs\ArchiveStripeProduct;
use App\Jobs\DeleteCoverFile;
use App\Models\Product;
use App\Models\ProductVariant;
use App\Models\Release;
use App\Models\Track;
use ClickHouseDB\Client;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ArtistStudioService
{
    public function __construct(
        public Client $clickhouse,
        public GetArtistStreams $getArtistStreams,
        public GetUserTracks $getUserTracks,
        public GetUserReleases $getUserReleases,
        public MinioService $minioService,
        public ArchiveStripeProduct $archiveStripeProduct,
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
                date DESC
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
                date DESC
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

        if ($tracks->isEmpty()) {
            return collect();
        }

        return collect($rows)
            ->filter(fn ($row) => $tracks->has((int) $row['track_id']))
            ->map(function ($row) use ($tracks) {
                $track = $tracks->get((int) $row['track_id']);

                return [
                    'track' => $track,
                    'streams' => (int) $row['streams'],
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

        $releaseIds = collect($rows)
            ->pluck('release_id')
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        if ($releaseIds->isEmpty()) {
            return collect();
        }

        $releases = Release::where('user_id', $artistId)
            ->whereIn('id', $releaseIds->all())
            ->get()
            ->keyBy('id');

        return collect($rows)
            ->filter(fn ($row) => $releases->has((int) $row['release_id']))
            ->map(function ($row) use ($releases) {
                $release = $releases->get((int) $row['release_id']);
                $release->plays = (int) $row['streams'];

                return $release;
            })
            ->values();
    }

    public function dropMerch(Request $request): void
    {
        $data = $request->validate([
            'item_title' => 'required|string|max:55',
            'item_description' => 'nullable|string|max:255',
            'images' => 'nullable|array',
            'images.*' => 'image|max:5120',
            'merch_variants' => 'required|json'
        ]);

        $merchVariants = json_decode($data['merch_variants'], true);

        validator([
            'merch_variants' => $merchVariants,
        ], [
            'merch_variants' => ['required', 'array', 'min:1'],
            'merch_variants.*.variant_name' => ['required', 'string', 'max:55'],
            'merch_variants.*.price' => ['required', 'numeric', 'min:0.01'],
            'merch_variants.*.stock' => ['required', 'integer', 'min:0'],
        ])->validate();

        $itemTitle = $data['item_title'];
        $itemDescription = $data['item_description'] ?? null;
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
            ]);

            $product->productImages()->createMany(
                array_map(fn($cover) => ['image_url' => $cover], $coverPaths)
            );

            foreach($merchVariants as $variant){
                ProductVariant::create([
                    'product_id' => $product->id,
                    'variant_name' => $variant['variant_name'],
                    'price' => $variant['price'],
                    'stock' => $variant['stock'],
                ]);
            }

            DB::afterCommit(function () use ($product) {
                Log::info('New Merch was stored', [
                    'title' => $product->title,
                    'user_id' => $product->user_id,
                ]);
            });
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
        $data = $request->validate([
            'item_title' => ['required', 'string', 'max:55'],
            'item_description' => ['nullable', 'string', 'max:255'],
            'existing_images' => ['nullable', 'json'],
            'new_images' => ['nullable', 'array'],
            'new_images.*' => ['image', 'max:5120'],
            'merch_variants' => ['required', 'json'],
        ]);

        $existingImages = json_decode($data['existing_images'], true) ?? [];
        $existingImageUrls = array_column($existingImages, 'image_url');
        $merchVariants = json_decode($data['merch_variants'], true) ?? [];

        validator([
            'existing_images' => $existingImages,
            'merch_variants' => $merchVariants,
        ], [
            'existing_images' => ['array'],
            'existing_images.*.id' => ['required', 'integer', 'exists:product_images,id'],
            'existing_images.*.image_url' => ['required', 'string', 'max:2048'],
            'merch_variants' => ['required', 'array', 'min:1'],
            'merch_variants.*.id' => ['nullable', 'integer', 'exists:product_variants,id'],
            'merch_variants.*.variant_name' => ['required', 'string', 'max:55'],
            'merch_variants.*.price' => ['required', 'numeric', 'min:0.01'],
            'merch_variants.*.stock' => ['required', 'integer', 'min:0'],
        ])->validate();

        DB::transaction(function () use (
            $request,
            $merch,
            $existingImageUrls,
            $merchVariants,
            $data,
        ) {
            $imagesToDelete = $merch->productImages()
                ->whereNotIn('image_url', $existingImageUrls)
                ->get();

            $imageUrlsToDelete = $imagesToDelete
                ->pluck('image_url')
                ->filter()
                ->values()
                ->all();

            $imagesIdsToDelete = $imagesToDelete
                ->pluck('id')
                ->all();

            $merch->productImages()
                ->whereIn('id', $imagesIdsToDelete)
                ->delete();

            DB::afterCommit(function () use ($imageUrlsToDelete) {
                foreach ($imageUrlsToDelete as $imageUrl) {
                    DeleteCoverFile::dispatch($imageUrl);
            }});

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
                'title' => $data['item_title'],
                'description' => $data['item_description'],
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

    public function deleteMerch($merchId): void
    {
        $merch = Product::with(['productVariants', 'productImages'])
            ->where('id', $merchId)
            ->firstOrFail();

        $merch->update([
            'status' => 'inactive'
        ]);

        ArchiveStripeProduct::dispatch($merch);

        Log::info('Merch was archived', [
            'merch_id' => $merchId,
            'stripe_product_id' => $merch->stripe_product_id,
            'deleted_variants_count' => $merch->productVariants->count(),
            'deleted_images_count' => $merch->productImages->count(),
        ]);
    }

    public function publishMerch(Product $merch): void
    {
        $merch->update([
            'status' => 'active'
        ]);

        $merch->save();

        Log::info('Merch status was changed', [
            'merch_id' => $merch->id,
            'status' => $merch->status
        ]);
    }
}
