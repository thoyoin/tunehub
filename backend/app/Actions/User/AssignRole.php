<?php

declare(strict_types=1);

namespace App\Actions\User;

use App\Models\Role;
use App\Models\User;

class AssignRole
{
    public function handle(User $user, string $role): User
    {
        $roleId = Role::where('slug', $role)->value('id');

        $user->roles()->syncWithoutDetaching([$roleId]);

        return $user;
    }
}
