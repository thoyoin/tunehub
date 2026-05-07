<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
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

        $totalPrice = 0;

        $variants = ProductVariant::with('product')
            ->whereIn('id', $cartItems->pluck('variant_id')->all())
            ->get()
            ->keyBy('id');

        $orderItems = [];

        $lineItems = $cartItems->map(function (array $item) use ($variants, &$orderItems, &$totalPrice) {
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

            $quantity = (int) $item['quantity'];
            $unitPrice = (int) $variant->price;
            $itemTotal = $unitPrice * $quantity;

            $totalPrice += $itemTotal;

            $orderItems[$variant->product_id] = [
                'product_variant_id' => $variant->id,
                'quantity' => $quantity,
                'unit_price' => $unitPrice,
                'subtotal' => $itemTotal,
                'title_snapshot' => $variant->variant_name
            ];

            return [
                'price' => $variant->stripe_price_id,
                'quantity' => $quantity,
            ];
        })->values()->all();

        $order = Order::create([
            'user_id' => auth()->id(),
            'status' => 'pending',
            'total_price' => $totalPrice,
        ]);

        $order->products()->attach($orderItems);

        $session = $this->stripeClient->checkout->sessions->create([
            'mode' => 'payment',
            'line_items' => $lineItems,
            'success_url' => (string) config('app.frontend_url.merch.success'),
            'cancel_url' => (string) config('app.frontend_url.merch.cancel'),
            'customer_email' => (string) $request->user()->email,
            'metadata' => [
                'order_id' => (string) $order->id,
                'user_id' => (string) $request->user()->id,
            ],
            'payment_intent_data' => [
                'metadata' => [
                    'order_id' => (string) $order->id,
                    'user_id' => (string) $request->user()->id,
                ]
            ],
        ]);

        $order->update([
            'stripe_session_id' => $session->id,
        ]);

        return $session->url;
    }
}
