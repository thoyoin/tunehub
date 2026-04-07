<?php

namespace App\Policies;

use App\Models\Product;
use App\Models\User;

class ProductPolicy
{
    public function before(User $user)
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

    /**
     * Create a new policy instance.
     */
    public function __construct()
    {
        //
    }

    public function create(User $user): bool
    {
        return $user->roles()
                ->where('slug', 'premium')
                ->exists()
            || $user->is_subscribed;
    }

    public function update(User $user, Product $product): bool
    {
        return $product->user_id === $user->id;
    }

    public function delete(User $user, Product $product): bool
    {
        return $product->user_id === $user->id;
    }

    public function publish(User $user, Product $product): bool
    {
        return $product->user_id === $user->id
            && ($product->status === 'approved'
            || $product->status === 'inactive');
    }
}
