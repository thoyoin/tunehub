<?php

namespace App\Policies;

use App\Models\LibraryItem;
use App\Models\User;

class LibraryItemPolicy
{
    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function before(User $user): bool | null
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

    public function show(User $user, LibraryItem $libraryItem): bool
    {
        return $user->id === $libraryItem->user_id;
    }
}
