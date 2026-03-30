<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminPanel;

use App\Actions\AdminPanel\merch\GetAllMerch;
use App\Actions\AdminPanel\merch\UpdateMerchStatus;
use App\Models\Product;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MerchController
{
    public function get(GetAllMerch $getAllMerch, Request $request): JsonResponse
    {
        $merch = $getAllMerch->handle($request);

        return response()->json([
            'merch' => $merch
        ]);
    }

    public function updateStatus(
        Product $merch,
        UpdateMerchStatus $merchStatus,
        Request $request,
    ): JsonResponse
    {
        $merchStatus->handle($request, $merch);

        return response()->json([
            'message' => 'Merch status updated successfully.'
        ]);
    }
}
