<?php

declare(strict_types=1);

namespace App\Jobs;

use App\Models\Product;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Foundation\Queue\Queueable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Stripe\StripeClient;

class ArchiveStripeProduct implements ShouldQueue
{
    use Queueable, SerializesModels, InteractsWithQueue, Dispatchable;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Product $merch,
    )
    {}

    /**
     * Execute the job.
     */
    public function handle(StripeClient $stripeClient): void
    {
        if (! $this->merch->stripe_product_id) {
            return;
        }

        foreach ($this->merch->productVariants as $variant) {
            if ($variant->stripe_price_id) {
                $stripeClient->prices->update($variant->stripe_price_id, [
                    'active' => false,
                ]);
            }
        }

        $stripeClient->products->update($this->merch->stripe_product_id, [
            'active' => false,
        ]);
    }
}
