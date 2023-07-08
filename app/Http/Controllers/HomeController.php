<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;


class HomeController extends Controller
{
    public function index(Request $request)
    {
        if (Auth::check()) {
            return to_route('auth.dashboard');
        } else {
            return to_route('auth.login');
        }
    }
}
