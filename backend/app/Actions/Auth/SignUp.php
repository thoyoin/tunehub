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
        ]);

        $user->roles()->attach(1, ['started_at' => now(), 'ends_at' => now()->addMonth()]);

        Auth::login($user);

        return $user;
    }
}
