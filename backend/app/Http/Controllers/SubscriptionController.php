<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SubscriptionService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SubscriptionController extends Controller
{
    public function __construct(
        public SubscriptionService $subscriptionService
    )
    {}

    public function goToCheckout(Request $request,): JsonResponse
    {
        $url = $this->subscriptionService->goToCheckout($request);

        return response()->json([
            'url' => $url
        ]);
    }

    public function getDetails(Request $request): JsonResponse
    {
        $data = $this->subscriptionService->getDetails($request);

        return response()->json([
            'details' => $data,
        ]);
    }

    public function goToBillingPortal(Request $request): JsonResponse
    {
        $url = $this->subscriptionService->goToBillingPortal($request);

        return response()->json([
            'url' => $url
        ]);
    }
}
