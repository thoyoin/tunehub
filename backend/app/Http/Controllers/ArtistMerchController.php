<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Models\Product;
use App\Services\ArtistMerchService;
use Illuminate\Http\JsonResponse;

class ArtistMerchController extends Controller
{
    public function get(string $slug, ArtistMerchService $artistMerchService): JsonResponse
    {
        $merch = $artistMerchService->get($slug);

        return response()->json([
            'merch' => $merch
        ]);
    }
}
