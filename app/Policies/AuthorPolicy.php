<?php

namespace App\Policies;

use App\Models\Author;
use App\Models\User;

class AuthorPolicy
{
    /**
     * Determine whether the user can view any authors.
     */
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view authors
    }

    /**
     * Determine whether the user can view the author.
     */
    public function view(User $user, Author $author): bool
    {
        return true; // Everyone can view an author
    }

    /**
     * Determine whether the user can create authors.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    /**
     * Determine whether the user can update the author.
     */
    public function update(User $user, Author $author): bool
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    /**
     * Determine whether the user can delete the author.
     */
    public function delete(User $user, Author $author): bool
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }
}
