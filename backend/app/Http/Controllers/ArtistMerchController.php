<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ArtistMerchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtistMerchController extends Controller
{
    public function get(string $slug, ArtistMerchService $artistMerchService): JsonResponse
    {
        $merch = $artistMerchService->get($slug);

        return response()->json([
            'merch' => $merch
        ]);
    }

    public function goToCheckout(ArtistMerchService $artistMerchService, Request $request): JsonResponse
    {
        $url = $artistMerchService->goToCheckout($request);

        return response()->json([
            'url' => $url
        ]);
    }
}
