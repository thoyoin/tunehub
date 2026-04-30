<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {}

    public function destroy(User $admin, User $target): bool
    {
        if ($admin->id === $target->id) {
            return false;
        }

        if ($target->roles()->where('slug', 'admin')->exists()) {
            return false;
        }

        return $admin->roles()->where('slug', 'admin')->exists();
    }
}
