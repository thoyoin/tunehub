<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\SearchService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class SearchController
{
    public function __construct(
        public SearchService $searchService
    )
    {}

    public function getContent(Request $request): JsonResponse
    {
        $content = $this->searchService->getContent($request);

        return response()->json($content);
    }

    public function getUsers(Request $request): JsonResponse
    {
        $users = $this->searchService->getUsers($request);

        return response()->json($users);
    }
}
