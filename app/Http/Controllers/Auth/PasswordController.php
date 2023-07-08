<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\ChangePasswordRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PasswordController extends Controller
{

    /**
     * Change the password Page
     * 
     */
    public function getChangePassword()
    {
        return view('auth.password.change');
    }

    /**
     * POST: change the password
     * @param Request $request
     */
    public function postChangePassword(ChangePasswordRequest $request)
    {
        $request->validated([
            'password_old' => 'required',
            'password' => 'required',
        ]);

        if ($request->validated()) {
            return redirect()->route('auth.password.change');
        }


        $user = User::find(Auth::user()->getAuthIdentifier());

        $this->auth->user()->setPassword($request->getParam('password'));

        return redirect()->route('home')->with('info', 'Your password was changed');
    }
}
