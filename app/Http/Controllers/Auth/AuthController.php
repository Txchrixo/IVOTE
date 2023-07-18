<?php

namespace App\Http\Controllers\Auth;

use App\Support\Audit;
use App\Http\Controllers\Controller;
use App\Http\Requests\LoginUserRequest;
use App\Http\Requests\RegisterUserRequest;
use App\Models\User;
use Illuminate\Auth\Events\Failed;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;
use App\Enums\Role;

class AuthController extends Controller
{
    /** bcrypt hash of a random string, used to keep failed logins constant-time. */
    private const DUMMY_HASH = '$2y$12$elHHl/fLTrLucPrDh1vpAejVweWf/lNkFcEXXl34rzw1NQchQE/Ri';

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

    /**
     * Checks the password. Back-office accounts (EA, RA) then go through the second
     * factor (or its enrollment) before the session is authenticated.
     */
    public function postLogin(LoginUserRequest $request): RedirectResponse
    {
        $credentials = $request->validated();
        $user = User::where('username', $credentials['username'])->first();

        // Hash something even for unknown usernames so timing does not reveal accounts
        $valid = Hash::check($credentials['password'], $user?->password ?? self::DUMMY_HASH);

        if (!$user || !$valid) {
            event(new Failed('web', $user, $credentials));

            return to_route('auth.login')->with('error', __('validation.error.check-username&password'))->withInput([
                'username' => $credentials['username'],
            ]);
        }

        if ($user->requiresTwoFactor()) {
            $request->session()->regenerate();
            $request->session()->put('login.id', $user->getKey());
            $request->session()->put('login.expires_at', now()->addMinutes(5)->getTimestamp());

            return $user->hasTwoFactorEnabled()
                ? to_route('auth.two-factor.challenge')
                : to_route('auth.two-factor.setup');
        }

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('auth/dashboard');
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

        Audit::record('user.created', $user, ['role' => $user->role->label()]);

        return back()->with('success', 'RA account created.');
    }
}
