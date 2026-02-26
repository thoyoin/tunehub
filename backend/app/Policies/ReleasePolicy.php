<?php

namespace App\Policies;

use App\Models\Release;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class ReleasePolicy
{
    public function before(User $user): bool|null
    {
        if ($user->role === '3') {
            return true;
        }

        return null;
    }

    public function updateStatus(User $user, Release $release): bool
    {
        return $user->role === '3';
    }

    public function publish(User $user, Release $release): bool
    {
        return $user->role === '2' && $user->id = $release->user_id;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return false;
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Release $release): bool
    {
        return false;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->role === '2';
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Release $release): bool
    {
        return $user->id === $release->user_id && $user->role === '2';
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Release $release): bool
    {
        return $user->id === $release->user_id || $user->role === '3';
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Release $release): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Release $release): bool
    {
        return false;
    }
}
