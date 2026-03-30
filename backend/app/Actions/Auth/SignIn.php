<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class SignIn
{
    public function handle($request): void
    {
        $request->validated();

        if (!Auth::attempt($request->safe()->except('middle_name'))) {
            throw ValidationException::withMessages([
                'email' => ['The provided credentials do not match our records.'],
            ]);
        }

        $request->session()->regenerate();
    }
}
