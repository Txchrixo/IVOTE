<?php

namespace App\Providers;

use App\Models\Candidate;
use App\Models\User;
use App\Models\VoteList;
use App\Policies\CandidatePolicy;
use App\Policies\ElectionPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * @var array<class-string, class-string>
     */
    protected $policies = [
        VoteList::class => ElectionPolicy::class,
        Candidate::class => CandidatePolicy::class,
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Role gates used by the route groups (can:ea / can:ra)
        Gate::define('ea', fn (User $user) => $user->isEA());
        Gate::define('ra', fn (User $user) => $user->isRA());
    }
}
