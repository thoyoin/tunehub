<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminPanel;

use App\Actions\AdminPanel\User\GetAllUsers;
use App\Actions\AdminPanel\User\GetUserDetails;
use App\Models\User;
use App\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class UserController
{
    public function getAll(GetAllUsers $getAllUsers, Request $request): JsonResponse
    {
        $users = $getAllUsers->handle($request);

        return response()->json($users);
    }

    /**
     * @throws ValidationException
     */
    public function delete(User $user, UserService $userService): JsonResponse
    {
        Gate::authorize('destroy', $user);

        $userService->destroyFromAdmin($user);

        Log::info('Moderation: user was deleted', [
            'username' => $user->username,
        ]);

        return response()->json([
            'message' => 'User deleted successfully.'
        ]);
    }

    public function getDetails(User $user, GetUserDetails $getUserDetails): JsonResponse
    {
        $userDetails = $getUserDetails->handle($user);

        return response()->json($userDetails);
    }
}
