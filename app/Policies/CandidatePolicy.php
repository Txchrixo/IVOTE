<?php

namespace App\Policies;

use App\Models\Candidate;
use App\Models\User;

/**
 * Candidates are registered by the registration authority.
 */
class CandidatePolicy
{
    public function viewAny(User $user): bool
    {
        return $user->isEA() || $user->isRA();
    }

    public function create(User $user): bool
    {
        return $user->isRA();
    }

    public function update(User $user, Candidate $candidate): bool
    {
        return $user->isRA();
    }

    public function delete(User $user, Candidate $candidate): bool
    {
        return $user->isRA();
    }
}
