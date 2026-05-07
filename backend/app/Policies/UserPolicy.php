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

        if (!$admin->roles()->where('slug', 'admin')->exists()) {
            return false;
        }

        $isTargetAdmin = $target->roles()->where('slug', 'target-admin')->exists();

        if ($isTargetAdmin) {
            $adminCount = User::whereHas('roles', function ($query) {
                $query->where('slug', 'admin')
                    ->count();
            });

            if ($adminCount <= 1) {
                return false;
            }
        }

        return true;
    }
}
