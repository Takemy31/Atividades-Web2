<?php

namespace App\Policies;

use App\Models\Publisher;
use App\Models\User;

class PublisherPolicy
{
    /**
     * Determine whether the user can view any publishers.
     */
    public function viewAny(User $user): bool
    {
        return true; // Everyone can view publishers
    }

    /**
     * Determine whether the user can view the publisher.
     */
    public function view(User $user, Publisher $publisher): bool
    {
        return true; // Everyone can view a publisher
    }

    /**
     * Determine whether the user can create publishers.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    /**
     * Determine whether the user can update the publisher.
     */
    public function update(User $user, Publisher $publisher): bool
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }

    /**
     * Determine whether the user can delete the publisher.
     */
    public function delete(User $user, Publisher $publisher): bool
    {
        return $user->isAdmin() || $user->isBibliotecario();
    }
}
