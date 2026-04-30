<?php

namespace App\Policies;

use App\Models\Release;
use App\Models\User;

class ReleasePolicy
{
    public function before(User $user): bool|null
    {
        if (
            $user->roles()
                ->where('slug', 'admin')
                ->exists()
        ) {
            return true;
        }

        return null;
    }

    public function updateStatus(User $user, Release $release): bool
    {
        return $user->id === $release->user_id && (
                $user->roles()
                    ->where('slug', 'premium')
                    ->exists()
                || $user->is_subscribed
            );
    }

    public function publish(User $user, Release $release): bool
    {
        return (
            $user->roles()
                ->where('slug', 'premium')
                ->exists()
            || $user->is_subscribed
            ) && $user->id === $release->user_id && $release->status === 'approved';
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
        return $user->roles()
                ->where('slug', 'premium')
                ->exists()
            || $user->is_subscribed;
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, Release $release): bool
    {
        return $user->id === $release->user_id && (
            $user->roles()
                ->where('slug', 'premium')
                ->exists()
            || $user->is_subscribed
            );
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Release $release): bool
    {
        return $user->id === $release->user_id && (
            $user->roles()
                ->where('slug', 'admin')
                ->exists()
            || $user->is_subscribed
            );
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
