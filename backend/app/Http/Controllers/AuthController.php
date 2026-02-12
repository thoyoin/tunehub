<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Actions\Auth\SignIn;
use App\Actions\Auth\SignUp;
use App\Actions\LibraryItem\CreateLibraryItem;
use App\Actions\Playlist\CreateStarterPlaylist;
use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Services\SignUpService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function signIn(
        SignInFormRequest $request,
        SignIn $signIn,
    ): JsonResponse|RedirectResponse
    {
        $signIn->handle($request);

        return response()->json([
            'message' => 'logged in successfully',
        ]);
    }

    public function signUp(
        SignUpFormRequest $request,
        SignUpService $signUpService
    ): JsonResponse
    {
        $signUpService->handle($request);

        return response()->json([
            'message' => 'Register successfully',
        ]);
    }

    public function logout(Request $request): JsonResponse
    {
        try {
            Auth::guard('web')->logout();

            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return response()->json([
                'message' => 'Success'
            ]);
        } catch (\Exception $e) {

            return response()->json([
                'error' => $e->getMessage()
            ], 500);
        }
    }
}
