<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController
{
    public function getContent(Request $request, SearchService $searchService): JsonResponse
    {
        $content = $searchService->getContent($request);

        return response()->json($content);
    }

    public function getUsers(Request $request, SearchService $searchService): JsonResponse
    {
        $users = $searchService->getUsers($request);

        return response()->json($users);
    }
}
