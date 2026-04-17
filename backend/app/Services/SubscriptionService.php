<?php

declare(strict_types=1);

namespace App\Services;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Stripe\PaymentMethod;
use Stripe\Stripe;

class SubscriptionService
{
    public function goToCheckout(Request $request)
    {
        $user = $request->user();

        $priceId = $request->input('price_id');

        $user->createOrGetStripeCustomer();

        $checkout = $user->newSubscription('premium', $priceId)
            ->checkout([
                'success_url' => config('app.frontend_url.success'),
                'cancel_url' => config('app.frontend_url.cancel'),
            ]);

        if ($user->stripe_id) {
            $user->updateDefaultPaymentMethodFromStripe();
        }

        return $checkout->url;
    }

    public function getDetails(Request $request): array
    {
        $user = $request->user();
        $sub = $user->subscription('premium');

        if (!$sub || !$sub->valid()) {
            throw ValidationException::withMessages([
                'subscription' => ['Premium subscription is not active.'],
            ]);
        }

        $stripeSub = $sub->asStripeSubscription();
        $upcomingInvoice = $user->upcomingInvoice();
        $subscriptionItem = $stripeSub->items->data[0] ?? null;

        if (!$upcomingInvoice) {
            throw ValidationException::withMessages([
                'subscription' => ['Unable to fetch upcoming invoice for the current subscription.'],
            ]);
        }

        if (!$subscriptionItem || !$subscriptionItem->price || !$subscriptionItem->plan) {
            throw ValidationException::withMessages([
                'subscription' => ['Subscription pricing data is unavailable.'],
            ]);
        }

        $data = [
            'current_period_end' => Carbon::createFromTimestamp($upcomingInvoice->period_end)
                ->toFormattedDateString(),
            'current_period_start' => Carbon::createFromTimestamp($upcomingInvoice->period_start)
                ->toFormattedDateString(),
            'next_billing' => Carbon::create($upcomingInvoice
                ->date()
                ->toDateString()
            )->toFormattedDateString(),
            'amount' => $subscriptionItem
                ->price
                ->unit_amount,
            'interval' => $subscriptionItem
                ->plan
                ->interval,
            'currency' => $stripeSub->currency,
            'card' => [],
        ];

        Stripe::setApiKey(config('services.stripe.secret'));

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

        return $data;
    }

    public function goToBillingPortal(Request $request)
    {
        return $request->user()->billingPortalUrl(config('app.url') . '/');
    }
}
