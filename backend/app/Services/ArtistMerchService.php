<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Product;
use App\Models\ProductVariant;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
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
            ->whereIn('status', ['active', 'sold_out'])
            ->with(['productImages', 'productVariants', 'user'])
            ->first();
    }

    public function goToCheckout(Request $request): ?string
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

            if (!$variant) {
                throw ValidationException::withMessages([
                    'cart' => ['One of the selected variants does not exist.'],
                ]);
            }

            if ((int) $variant->product_id !== (int) $item['product_id']) {
                throw ValidationException::withMessages([
                    'cart' => ['Variant does not belong to the provided product.'],
                ]);
            }

            if (!$variant->product || !in_array($variant->product->status, ['active', 'sold_out'], true)) {
                throw ValidationException::withMessages([
                    'cart' => ['One of the selected products is unavailable for purchase.'],
                ]);
            }

            if ((int) $item['quantity'] > (int) $variant->stock) {
                throw ValidationException::withMessages([
                    'cart' => ['Requested quantity exceeds available stock.'],
                ]);
            }

            if (empty($variant->stripe_price_id)) {
                throw ValidationException::withMessages([
                    'cart' => ['One of the selected variants cannot be purchased right now.'],
                ]);
            }

            return [
                'price' => $variant->stripe_price_id,
                'quantity' => (int) $item['quantity'],
            ];
        })->values()->all();


        $session = $this->stripeClient->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => (string) config('app.frontend_url.success'),
            'cancel_url' => (string) config('app.frontend_url.cancel'),
            'customer_email' => (string) $request->user()->email,
            'metadata' => [
                'user_id' => (string) $request->user()->id,
            ]
        ]);

        return $session->url;
    }
}
