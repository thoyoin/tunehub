<?php

declare(strict_types=1);

namespace App\Http\Controllers\AdminPanel;

use App\Actions\AdminPanel\User\GetAllUsers;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController
{
    public function getAll(GetAllUsers $getAllUsers, Request $request): JsonResponse
    {
        $users = $getAllUsers->handle($request);

        return response()->json($users);
    }

    public function delete(User $user): JsonResponse
    {
        $user->delete();

        return response()->json([
            'message' => 'User deleted successfully.'
        ]);
    }
}
