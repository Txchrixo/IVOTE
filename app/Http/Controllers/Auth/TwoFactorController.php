<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Services\TwoFactorAuthenticator;
use App\Support\Audit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

/**
 * Second step of the login for back-office accounts: TOTP challenge, or enrollment
 * when the account has no second factor yet. The user is only authenticated once
 * this step succeeds.
 */
class TwoFactorController extends Controller
{
    public function __construct(private readonly TwoFactorAuthenticator $twoFactor)
    {
    }

    public function showChallenge(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);
        if (!$user || !$user->hasTwoFactorEnabled()) {
            return $this->restart($request);
        }

        return view('auth.two-factor.challenge');
    }

    public function postChallenge(Request $request): RedirectResponse
    {
        $user = $this->pendingUser($request);
        if (!$user || !$user->hasTwoFactorEnabled()) {
            return $this->restart($request);
        }

        $data = $request->validate([
            'code' => ['nullable', 'required_without:recovery_code', 'string', 'max:10'],
            'recovery_code' => ['nullable', 'required_without:code', 'string', 'max:32'],
        ]);

        $valid = !empty($data['code'])
            ? $this->twoFactor->verify($user, $data['code'])
            : $this->twoFactor->useRecoveryCode($user, $data['recovery_code']);

        if (!$valid) {
            Audit::record('auth.two_factor_failed', $user, [], $user->getKey());

            return back()->withErrors(['code' => __('The authentication code is invalid.')]);
        }

        if (empty($data['code'])) {
            Audit::record('auth.recovery_code_used', $user, [], $user->getKey());
        }

        return $this->completeLogin($request, $user);
    }

    public function showSetup(Request $request): View|RedirectResponse
    {
        $user = $this->pendingUser($request);
        if (!$user || $user->hasTwoFactorEnabled()) {
            return $this->restart($request);
        }

        // The secret stays in the session until the first code confirms it
        $secret = $request->session()->get('login.two_factor_secret');
        if (!$secret) {
            $secret = $this->twoFactor->generateSecret();
            $request->session()->put('login.two_factor_secret', $secret);
        }

        return view('auth.two-factor.setup', [
            'qrCode' => $this->twoFactor->qrCodeSvg($user->username, $secret),
            'secret' => $secret,
        ]);
    }

    public function postSetup(Request $request): RedirectResponse
    {
        $user = $this->pendingUser($request);
        $secret = $request->session()->get('login.two_factor_secret');
        if (!$user || !$secret || $user->hasTwoFactorEnabled()) {
            return $this->restart($request);
        }

        $data = $request->validate(['code' => ['required', 'string', 'max:10']]);

        if (!$this->twoFactor->verifySecret($secret, $data['code'])) {
            return back()->withErrors(['code' => __('The authentication code is invalid.')]);
        }

        $recoveryCodes = $this->twoFactor->enable($user, $secret);
        Audit::record('auth.two_factor_enabled', $user, [], $user->getKey());

        $this->completeLogin($request, $user);

        return to_route('auth.two-factor.recovery-codes')->with('two_factor_recovery_codes', $recoveryCodes);
    }

    /** Recovery codes are shown once, right after enrollment. */
    public function showRecoveryCodes(Request $request): View|RedirectResponse
    {
        $codes = $request->session()->get('two_factor_recovery_codes');
        if (!$codes) {
            return to_route('auth.dashboard');
        }

        return view('auth.two-factor.recovery-codes', ['codes' => $codes]);
    }

    private function pendingUser(Request $request): ?User
    {
        $id = $request->session()->get('login.id');
        $expiresAt = (int) $request->session()->get('login.expires_at', 0);

        if (!$id || $expiresAt < now()->getTimestamp()) {
            return null;
        }

        return User::find($id);
    }

    private function completeLogin(Request $request, User $user): RedirectResponse
    {
        $request->session()->forget(['login.id', 'login.expires_at', 'login.two_factor_secret']);
        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->intended('auth/dashboard');
    }

    private function restart(Request $request): RedirectResponse
    {
        $request->session()->forget(['login.id', 'login.expires_at', 'login.two_factor_secret']);

        return to_route('auth.login')->with('error', __('Your login session expired, please sign in again.'));
    }
}
