<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Services\MinioService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserController extends Controller
{
    public function update(Request $request, MinioService $minioService): JsonResponse
    {
        $data = $request->only(['username', 'email', 'profile_picture']);
        $user = Auth::user();

        if ($request->hasFile('profile_picture')) {
            $url = $minioService->storeProfile($request->file('profile_picture'));
            $data['profile_picture'] = $url;
        }

        $user->update($data);

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
