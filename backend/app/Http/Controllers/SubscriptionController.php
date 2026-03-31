<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class SubscriptionController extends Controller
{
    public function goToCheckout(Request $request): JsonResponse
    {
        $user = $request->user();

        $priceId = $request->input('price_id');

        $user->createOrGetStripeCustomer();

        $checkout = $user->newSubscription('premium', $priceId)
            ->checkout([
                'success_url' => 'http://127.0.0.1:5175/subscription/success',
                'cancel_url' => 'http://127.0.0.1:5175/subscription/cancel',
            ]);

        if ($user->stripe_id) {
            $user->updateDefaultPaymentMethodFromStripe();
        }

        return response()->json([
            'url' => $checkout->url
        ]);
    }

    public function getDetails(Request $request): JsonResponse
    {
        $user = $request->user();
        $sub = $user->subscription('premium');

        $stripeSub = $sub->asStripeSubscription();

        $data = [
            'current_period_end' => Carbon::createFromTimestamp($user
                ->upcomingInvoice()
                ->period_end
            )->toFormattedDateString(),
            'current_period_start' => Carbon::createFromTimestamp($user
                ->upcomingInvoice()
                ->period_start
            )->toFormattedDateString(),
            'next_billing' => Carbon::create($user
                ->upcomingInvoice()
                ->date()
                ->toDateString()
            )->toFormattedDateString(),
            'amount' => $stripeSub->items
                ->data[0]
                ->price
                ->unit_amount,
            'interval' => $stripeSub->items
                ->data[0]
                ->plan
                ->interval,
            'currency' => $stripeSub->currency,
            'card' => [],
        ];

        Stripe::setApiKey(env('STRIPE_SECRET'));

        $pm = null;

        if ($stripeSub->default_payment_method) {
            $pm = PaymentMethod::retrieve($stripeSub->default_payment_method);
        }

        if (!$pm && $user->stripe_id) {
            $methods = PaymentMethod::all([
                'customer' => $user->stripe_id,
                'type' => 'card',
                'limit' => 1,
            ]);

            $pm = $methods->data[0] ?? null;
        }

        if ($pm && $pm->card) {
            $card = $pm->card;

            $data['card'] = [
                'brand' => $card->brand,
                'last4' => $card->last4,
                'country' => $card->country,
                'funding' => $card->funding,
                'exp_month' => $card->exp_month,
                'exp_year' => $card->exp_year,
            ];
        }

        return response()->json([
            'details' => $data,
        ]);
    }

    public function goToBillingPortal(Request $request): JsonResponse
    {
        $url = $request->user()->billingPortalUrl(env('APP_URL') . '/');

        return response()->json([
            'url' => $url
        ]);
    }
}
