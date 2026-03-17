<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Support\Facades\Auth;

class GetAuthUser
{
    public function handle(): Authenticatable
    {
        $user = Auth::user();

        $user->load('roles');

        return $user;
    }
}
