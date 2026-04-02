<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\ArtistMerchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ArtistMerchController extends Controller
{
    public function __construct(
        public ArtistMerchService $artistMerchService,
    )
    {}

    public function get(string $slug): JsonResponse
    {
        $merch = $this->artistMerchService->get($slug);

        return response()->json([
            'merch' => $merch
        ]);
    }

    public function goToCheckout(Request $request): JsonResponse
    {
        $url = $this->artistMerchService->goToCheckout($request);

        return response()->json([
            'url' => $url
        ]);
    }
}
