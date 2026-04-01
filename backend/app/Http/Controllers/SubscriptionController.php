<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function goToCheckout(
        Request $request,
        SubscriptionService $subscriptionService
    ): JsonResponse {
        $url = $subscriptionService->goToCheckout($request);

        return response()->json([
            'url' => $url
        ]);
    }

    public function getDetails(Request $request, SubscriptionService $subscriptionService): JsonResponse
    {
        $data = $subscriptionService->getDetails($request);

        return response()->json([
            'details' => $data,
        ]);
    }

    public function goToBillingPortal(Request $request, SubscriptionService $subscriptionService): JsonResponse
    {
        $url = $subscriptionService->goToBillingPortal($request);

        return response()->json([
            'url' => $url
        ]);
    }
}
