<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function subscribe(Request $request): JsonResponse
    {
        $user = $request->user();

        $subscription = $user->newSubscription('premium', 'price_1TBa6jLLiCzKtruSYomynytQ')
            ->create($request->payment_method);

        $subscription->update([
            'ends_at' => now()->addMonth(),
        ]);

        return response()->json([
            'message' => 'Subscription successfully created',
        ]);
    }
}
