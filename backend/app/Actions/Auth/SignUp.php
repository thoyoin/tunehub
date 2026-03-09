<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class SignUp
{
    public function handle($request): Model|User
    {
        $user = User::query()->create([
            'username' => $request->get('username'),
            'email' => $request->get('email'),
            'password' => Hash::make($request->get('password')),
            'profile_picture' => 'http://localhost:9000/tunehub/defaults/profile_cover.jpg',
        ]);

        $user->roles()->attach(1);

        Auth::login($user);

        return $user;
    }
}
