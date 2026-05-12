<?php

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Stripe\StripeClient;

class CreateStripeProductForMerch implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public int $merchId,
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(StripeClient $stripeClient): void
    {
        $merch = Product::with(['productVariants', 'productImages'])
            ->findOrFail($this->merchId);

        $images = $merch->productImages
            ->pluck('image_url')
            ->filter(fn ($url) => is_string($url) && trim($url) !== '')
            ->map(fn ($url) => trim($url))
            ->values()
            ->take(8)
            ->all();

        $productPayload = [
            'name' => (string) $merch->title,
            'metadata' => [
                'artist_id' => (string) $merch->user_id,
                'merch_id' => (string) $merch->id,
            ],
        ];

        if (!empty($merch->description)) {
            $productPayload['description'] = trim($merch->description);
        }

        if (!empty($images)) {
            $productPayload['images'] = $images;
        }

        $stripeProduct = $stripeClient->products->create($productPayload);

        $merch->stripe_product_id = $stripeProduct->id;

        $merch->save();

        foreach ($merch->productVariants as $variant) {
            $price = $stripeClient->prices->create([
                'product' => $stripeProduct->id,
                'currency' => strtolower((string) $merch->currency),
                'unit_amount' => (int) $variant->price,
                'metadata' => [
                    'merch_id' => (string) $merch->id,
                    'variant_id' => (string) $variant->id,
                    'variant_name' => (string) $variant->variant_name,
                ],
            ]);

            $variant->stripe_price_id = $price->id;
            $variant->saveOrFail();
            $variant->refresh();
        }

        Log::info('Stripe merch published', [
            'merch_id' => $merch->id,
            'stripe_product_id' => $stripeProduct->id,
        ]);
    }
}
