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

            if ($session->amount_total !== (int)$order->total_price * 100) abort(400);

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

            Payment::updateOrCreate(
                [
                    'provider' => 'stripe',
                    'provider_payment_id' => $session->payment_intent ?? $session->id,
                ],
                [
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'currency' => $session->currency ?? 'usd',
                    'amount' => $order->total_price,
                    'status' => 'success',
                ]
            );

            $customerAddress = trim(
                ($session->customer_details->address->country ?? '') . ', ' .
                ($session->customer_details->address->city ?? '')
            );

            $paymentIntentId = $session->payment_intent ?? $session->id;

            $order->update([
                'status' => 'paid',
                'stripe_payment_intent_id' => $paymentIntentId,
                'customer_name' => $session->customer_details->name,
                'customer_email' => $session->customer_details->email,
                'customer_address' => $customerAddress,
            ]);
        });
    }

    public function handleCheckoutFailed($paymentIntent): void
    {
        $orderId = $paymentIntent->metadata->order_id ?? null;

        if (!$orderId) {
            return;
        }

        DB::transaction(function () use ($orderId, $paymentIntent) {
            $order = Order::query()
                ->lockForUpdate()
                ->findOrFail($orderId);

            if (!$order || $order->status === 'paid') {
                return;
            }

            Payment::updateOrCreate(
                [
                    'provider_payment_id' => $paymentIntent->id ?? null,
                    'provider' => 'stripe',
                ],
                [
                    'user_id' => $order->user_id,
                    'order_id' => $order->id,
                    'currency' => $paymentIntent->currency ?? 'usd',
                    'amount' => $order->total_price,
                    'status' => 'failed',
                ]
            );

            $order->update([
                'status' => 'failed',
                'stripe_payment_intent_id' => $paymentIntent->id ?? null,
            ]);
        });
    }
}
