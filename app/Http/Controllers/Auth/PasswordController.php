<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\View\View;

class PasswordController extends Controller
{
    public function getChangePassword(): View
    {
        return view('auth.password.change');
    }

    /**
     * Changes the password and logs out the user's other sessions.
     */
    public function postChangePassword(ChangePasswordRequest $request): RedirectResponse
    {
        $user = $request->user();
        $user->password = Hash::make($request->validated('password'));
        $user->save();

        Auth::logoutOtherDevices($request->validated('password'));
        $request->session()->regenerate();

        return to_route('auth.password.change')->with('info', 'Your password was changed.');
    }
}
