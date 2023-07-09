<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Access\Response;

class UserPolicy
{
       /**
     * Détermine si l'utilisateur est une autorité d'inscription (RA).
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function isRA(User $user)
    {
        return $user->role == Role::$RA;
    }
    
    /**
     * Détermine si l'utilisateur est une autorité electorale.
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function isEA(User $user)
    {
        return $user->role == Role::$EA;
    }

    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        //
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, User $model): bool
    {
        //
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, User $model): bool
    {
        //
    }
}
