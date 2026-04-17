<?php

declare(strict_types=1);

namespace App\Actions\AdminPanel\User;

use App\Models\User;

class GetUserDetails
{
    public function handle(User $user): User
    {
        return $user->loadCount(['playlists', 'tracks'])
            ->load('roles');
    }
}
