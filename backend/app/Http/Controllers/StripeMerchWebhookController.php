<?php

namespace App\Http\Controllers;

use App\Services\StripeMerchWebhookService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class StripeMerchWebhookController extends Controller
{
    public function handle(Request $request, StripeMerchWebhookService $stripeWebhookService): JsonResponse
    {
        $payload = $request->getContent();

        $sigHeader = $request->header('Stripe-Signature');

        try {
            $event = \Stripe\Webhook::constructEvent(
                $payload,
                $sigHeader,
                config('services.stripe.merch_webhook_secret')
            );
        } catch(\UnexpectedValueException $e) {
            Log::warning('Invalid stripe merch webhook payload', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['result' => 'invalid payload',], 400);
        } catch(\Stripe\Exception\SignatureVerificationException $e) {
            Log::warning('Invalid stripe merch webhook signature', [
                'exception' => $e->getMessage(),
            ]);

            return response()->json(['result' => 'invalid signature',], 400);
        }

        if ($event->type === 'checkout.session.completed') {
            $stripeWebhookService->handleCheckoutCompleted($event->data->object);
        }

        if ($event->type === 'payment_intent.payment_failed') {
            $stripeWebhookService->handleCheckoutFailed($event->data->object);
        }

        return response()->json(['status' => 'success']);
    }
}
