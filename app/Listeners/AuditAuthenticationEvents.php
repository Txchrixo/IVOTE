<?php

namespace App\Listeners;

use App\Support\Audit;
use Illuminate\Auth\Events\Failed;
use Illuminate\Auth\Events\Lockout;
use Illuminate\Auth\Events\Login;
use Illuminate\Auth\Events\Logout;
use Illuminate\Events\Dispatcher;

/**
 * Writes logins, failed logins, lockouts and logouts to the audit log.
 */
class AuditAuthenticationEvents
{
    public function handleLogin(Login $event): void
    {
        Audit::record('auth.login', $event->user, [], $event->user->getAuthIdentifier());
    }

    public function handleFailed(Failed $event): void
    {
        Audit::record('auth.failed', null, [
            'username' => mb_substr((string) ($event->credentials['username'] ?? ''), 0, 64),
        ], $event->user?->getAuthIdentifier());
    }

    public function handleLockout(Lockout $event): void
    {
        Audit::record('auth.lockout', null, [
            'username' => mb_substr((string) $event->request->input('username'), 0, 64),
        ]);
    }

    public function handleLogout(Logout $event): void
    {
        if ($event->user) {
            Audit::record('auth.logout', $event->user, [], $event->user->getAuthIdentifier());
        }
    }

    public function subscribe(Dispatcher $events): array
    {
        return [
            Login::class => 'handleLogin',
            Failed::class => 'handleFailed',
            Lockout::class => 'handleLockout',
            Logout::class => 'handleLogout',
        ];
    }
}
