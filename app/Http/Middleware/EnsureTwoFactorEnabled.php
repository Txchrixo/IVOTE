<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

/**
 * Back-office accounts without a confirmed second factor (e.g. sessions opened
 * before it became mandatory) are signed out and must log in again.
 */
class EnsureTwoFactorEnabled
{
    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user();

        if ($user && $user->requiresTwoFactor() && !$user->hasTwoFactorEnabled()) {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();

            return to_route('auth.login')->with('error', __('Two-factor authentication is required, please sign in again.'));
        }

        return $next($request);
    }
}
