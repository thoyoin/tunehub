<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController
{
    public function index(Request $request, SearchService $searchService): JsonResponse
    {
        $content = $searchService->index($request);

        return response()->json($content);
    }
}
