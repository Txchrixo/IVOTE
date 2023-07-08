<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Redirect;
use Symfony\Component\HttpFoundation\Response;

class RAMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        /*
        if (!Auth::check()) {
            session()->flash('error', 'Please sign in before doing that');
            return Redirect::route('auth.login');
        } elseif (Auth::user()->role != 2) {
            session()->flash('error', 'This page is only shown to the election authority');
            return Redirect::route('home');
        }*/

        return $next($request);
    }
}
