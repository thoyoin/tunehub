<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(Request $request, UserService $userService): JsonResponse
    {
        $user = $userService->update($request);

        return response()->json([
            'message' => 'Profile updated successfully',
            'Updated user:' => $user,
        ]);
    }

    public function get(): JsonResponse
    {
        $user = Auth::user();

        return response()->json([
            'user' => $user,
        ]);
    }
}
