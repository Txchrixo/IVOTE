<?php

namespace App\Http\Middleware;

use App\Enums\Role;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class EAMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
    
        if (!Auth::check()) {
            return Redirect::route('auth.login')->with('error', 'Please sign in before doing that');
        } elseif (Auth::user()->role != Role::$EA) {
            return Redirect::route('auth.login')->with('error', 'This page is only shown to the election authority');
        }

        return $next($request);
    }
}
