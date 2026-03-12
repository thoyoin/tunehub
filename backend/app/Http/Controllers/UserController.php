<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Auth\GetAuthUser;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

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

    public function get(GetAuthUser $getAuthUser): JsonResponse
    {
        return response()->json([
            'user' => $getAuthUser->handle(),
        ]);
    }
}
