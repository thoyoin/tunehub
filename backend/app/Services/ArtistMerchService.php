<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Stripe\StripeClient;

class ArtistMerchService
{
    public function __construct(
        public StripeClient $stripeClient
    )
    {}

    public function get($slug)
    {
        return Product::where('slug', $slug)
            ->where('status', 'active' || 'soldout')
            ->with(['productImages', 'productVariants', 'user'])
            ->first();
    }

    public function goToCheckout(Request $request)
    {
        $validated = $request->validate([
            'cart' => ['required', 'array', 'min:1'],
            'cart.*.product_id' => ['required', 'integer', 'exists:products,id'],
            'cart.*.variant_id' => ['required', 'integer', 'exists:product_variants,id'],
            'cart.*.quantity' => ['required', 'integer', 'min:1'],
        ]);

        $cartItems = collect($validated['cart']);

        $variants = ProductVariant::with('product')
            ->whereIn('id', $cartItems->pluck('variant_id')->all())
            ->get()
            ->keyBy('id');

        $lineItems = $cartItems->map(function (array $item) use ($variants) {
            $variant = $variants->get($item['variant_id']);

            return [
                'price' => $variant->stripe_price_id,
                'quantity' => (int) $item['quantity'],
            ];
        })->values()->all();

        $session = $this->stripeClient->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => 'http://127.0.0.1:5175/merch/payment/success',
            'cancel_url' => 'http://127.0.0.1:5175//merch/payment/cancel',
            'customer_email' => (string) $request->user()->email,
            'metadata' => [
                'user_id' => (string) $request->user()->id,
            ]
        ]);

        return $session->url;
    }
}
