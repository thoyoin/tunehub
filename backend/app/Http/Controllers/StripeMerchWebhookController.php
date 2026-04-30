<?php

namespace App\Http\Controllers;

use App\Services\StripeMerchWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class StripeMerchWebhookController extends Controller
{
    public function handle(Request $request, StripeMerchWebhookService $stripeWebhookService): JsonResponse
    {
        $payload = $request->getContent();

        $sigHeader = $request->header('Stripe-Signature');

        $event = \Stripe\Webhook::constructEvent(
            $payload,
            $sigHeader,
            config('services.stripe.merch_webhook_secret')
        );

        if ($event->type === 'checkout.session.completed') {
            $stripeWebhookService->handleCheckoutCompleted($event->data->object);
        }

        if ($event->type === 'payment_intent.payment_failed') {
            $stripeWebhookService->handleCheckoutFailed($event->data->object);
        }

        return response()->json(['status' => 'success']);
    }
}
