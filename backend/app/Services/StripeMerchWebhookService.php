<?php

declare(strict_types=1);

namespace App\Services;

use App\Models\Order;
use App\Models\Payment;
use App\Models\ProductVariant;
use Illuminate\Support\Facades\DB;
use RuntimeException;

class StripeMerchWebhookService
{
    public function handleCheckoutCompleted($session): void
    {
        $orderId = $session->metadata->order_id ?? null;

        if (!$orderId) {
            throw new RuntimeException('Missing order id in Stripe session metadata');
        }

        DB::transaction(function () use ($orderId, $session) {
            $order = Order::query()
                ->with('products')
                ->lockForUpdate()
                ->findOrFail($orderId);

            if ($order->status === 'paid') {
                return;
            }

            foreach ($order->products as $product) {
                $variantId = $product->pivot->product_variant_id;
                $quantity = (int) $product->pivot->quantity;

                $variant = ProductVariant::query()
                    ->lockForUpdate()
                    ->findOrFail($variantId);

                if ($variant->stock < $quantity) {
                    throw new RuntimeException('Out of stock');
                }

                $variant->decrement('stock', $quantity);
            }

            Payment::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'provider' => 'stripe',
                'provider_payment_id' => $session->payment_intent ?? $session->id,
                'currency' => $order->currency ?? $session->currency ?? 'usd',
                'amount' => $order->total_price,
                'status' => 'success',
            ]);

            $order->update([
                'status' => 'paid',
                'stripe_payment_intent_id' => $session->payment_intent,
                'customer_name' => $session->customer_details->name,
                'customer_email' => $session->customer_details->email,
                'customer_address' => $session->customer_details->address->country .
                    ', ' . $session->customer_details->address->city,
            ]);
        });
    }

    public function handleCheckoutFailed($session): void
    {
        $orderId = $session->metadata->order_id ?? null;

        if (!$orderId) {
            return;
        }

        DB::transaction(function () use ($orderId, $session) {
            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($orderId);

            if (!$order || $order->status === 'paid') {
                return;
            }

            Payment::create([
                'user_id' => $order->user_id,
                'order_id' => $order->id,
                'provider' => 'stripe',
                'provider_payment_id' => $session->payment_intent ?? $session->id,
                'currency' => $order->currency ?? $session->currency ?? 'usd',
                'amount' => $order->total_price,
                'status' => 'failed',
            ]);

            $order->update([
                'status' => 'failed',
                'stripe_payment_intent_id' => $session->payment_intent,
            ]);
        });
    }
}
