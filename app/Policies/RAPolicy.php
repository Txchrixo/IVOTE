<?php

namespace App\Policies;

use App\Enums\Role;
use App\Models\User;

class RAPolicy
{
    /**
     * Détermine si l'utilisateur est une autorité d'inscription (RA).
     *
     * @param  \App\Models\User  $user
     * @return bool
     */
    public function isRA(User $user)
    {
        return true;
    }
}
