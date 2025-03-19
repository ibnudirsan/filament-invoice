<?php

namespace App\Policies;

use App\Models\User;
use Spatie\Permission\Models\Role;

class RolePolicy
{
    /**
     * Determine whether the user can update the role.
     *
     * @param User $user The user attempting to update the role.
     * @param Role $role The role being updated.
     * @return bool True if the user can update the role, false otherwise.
     */
    public function update(User $user, Role $role): bool
    {
        if ($user->hasRole('super-admin')) {
            return true;
        }

        if ($user->hasRole('super-admin')) {
            return false;
        }
            return $user->can('roles.update');
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('roles.viewAny');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, Role $role): bool
    {
        return $user->can('roles.view');
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('roles.create');
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Role $role): bool
    {
        return $user->can('roles.delete');
    }

    /**
     * Determine whether the user can delete any users.
     *
     * @param User $user The user to check.
     * @return bool True if the user can delete any users, false otherwise.
     */
    
    public function deleteAny(User $user): bool
    {
        return $user->can('roles.deleteAny');
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Role $role): bool
    {
        return false;
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Role $role): bool
    {
        return false;
    }
}
