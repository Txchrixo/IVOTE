<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use App\Enums\Role;

class AuthController extends Controller
{

    public function getLoginForm(): View
    {
        return view('auth.login');
    }

    public function getRegisterForm(): View
    {
        return view('auth.register');
    }

    public function logOut(Request $request): RedirectResponse
    {
        Auth::logout();
        // Invalide la session et régénère le jeton CSRF (évite la réutilisation de session)
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return to_route('auth.login');
    }

    public function getDashboard(): RedirectResponse | View
    {
        $role = Auth::user()->role;

        if ($role === Role::RA) {
            return to_route('ra.dashboard')->with('success', 'You are logged in as a RA');
        }

        if ($role === Role::EA) {
            return to_route('ea.dashboard')->with('success', 'You are logged in as an EA');
        }

        return back()->with('error', 'You are not logged in');
    }

    public function postLogin(LoginUserRequest $request): RedirectResponse | View
    {
        $credentials = $request->validated();
        if (Auth::attempt($credentials)) {
            $request->session()->regenerate();
            return redirect()->intended('auth/dashboard');
        }
        return to_route('auth.login')->with('error', __('validation.error.check-username&password'))->withInput([
            'username' => $credentials['username']
        ]);
    }

    /**
     * Création d'un compte RA par l'EA connecté (route protégée par le middleware EA).
     * Le rôle est imposé côté serveur et l'EA reste connecté.
     */
    public function postRegister(RegisterUserRequest $request): RedirectResponse
    {
        $credentials = $request->validated();

        $user = new User();
        $user->username = $credentials['username'];
        $user->password = Hash::make($credentials['password']);
        $user->role = Role::RA;
        $user->save();

        Log::info('RA account created', ['created_user_id' => $user->id, 'by' => Auth::id()]);

        return back()->with('success', 'RA account created.');
    }
}
