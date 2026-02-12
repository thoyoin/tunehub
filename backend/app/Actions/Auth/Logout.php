<?php

declare(strict_types=1);

namespace App\Actions\Auth;

use Illuminate\Support\Facades\Auth;

class Logout
{
    public function handle(): void
    {
        Auth::guard('web')->logout();
    }
}
