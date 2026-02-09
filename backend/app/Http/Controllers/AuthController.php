<?php

declare(strict_types=1);

namespace App\Http\Controllers;

use App\Http\Requests\SignInFormRequest;
use App\Http\Requests\SignUpFormRequest;
use App\Models\LibraryItem;
use App\Models\Playlist;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function signIn(SignInFormRequest $request): JsonResponse|RedirectResponse
    {
        if (! Auth::attempt($request->validated())) {
            return back()->withErrors([
                'email' => 'The provided credentials do not match our records.',
            ]);
        }

        $request->session()->regenerate();

        return response()->json([
            'message' => 'logged in successfully',
        ]);
    }

    public function signUp(SignUpFormRequest $request): JsonResponse
    {
        $user = User::query()->create([
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'role' => '2',
        ]);

        $playlist = Playlist::create([
            'title' => 'Liked tracks',
            'description' => null,
            'user_id' => $user->id,
            'cover_url' => 'http://localhost:9000/tunehub/defaults/likedtracks.png',
        ]);

        LibraryItem::create([
            'user_id' => $user->id,
            'item_type' => 'playlist',
            'item_id' => $playlist->id,
        ]);

        Auth::login($user);

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

            return response()->json(['message' => 'Success']);
        } catch (\Exception $e) {
            return response()->json(['error' => $e->getMessage()], 500);
        }
    }
}
