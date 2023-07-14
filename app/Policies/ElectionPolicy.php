<?php

namespace App\Policies;

use App\Models\User;
use App\Models\VoteList;

/**
 * Elections are managed by the election authority only.
 */
class ElectionPolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEA() || $user->isRA();
    }

    public function view(User $user, VoteList $election): bool
    {
        return $this->viewAny($user);
    }

    public function create(User $user): bool
    {
        return $user->isEA();
    }

    public function update(User $user, VoteList $election): bool
    {
        return $user->isEA();
    }

    public function changeStage(User $user, VoteList $election): bool
    {
        return $user->isEA();
    }

    public function delete(User $user, VoteList $election): bool
    {
        return $user->isEA();
    }
}
