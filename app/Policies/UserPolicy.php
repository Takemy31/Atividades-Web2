<?php

namespace App\Policies;

use App\Models\User;

class UserPolicy
{
    /**
     * Determine whether the user can view any users.
     */
    public function viewAny(User $user): bool
    {
        return true; // Anyone can view the list of users
    }

    /**
     * Determine whether the user can view the user.
     */
    public function view(User $user, User $model): bool
    {
        return true; // Anyone can view a user profile
    }

    /**
     * Determine whether the user can update the user (only admin can change roles).
     */
    public function update(User $user, User $model): bool
    {
        // Only admins can update users
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can change user roles (only admin).
     */
    public function updateRole(User $user, User $model): bool
    {
        return $user->isAdmin();
    }
}
