<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

        return response()->json([
            'url' => $checkout->url
        ]);
    }
}
