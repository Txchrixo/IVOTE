<?php

namespace App\Http\Controllers\EA;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

/**
 * Class EASettingController
 * @author TxChrixo
 * The EASetting Controller to show the page of setting in EA users
 * @package Controllers\EA
 */
class EASettingController extends Controller
{
    /**
     * user setting Pages
     * @return View
    */
    public function getSetting() : View
    {
        $data = Auth::user();
        return view('ea.setting', ['user' => $data]);
    }

    /**
     * POST: update Bitcoin Address & Keys
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postBitcoinSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'bitcoin_address' => 'bail|required|max:255',
            'bitcoin_priv_key' => 'bail|required|max:255',
        ]);


        Auth::user()->update($validator->safe()->all());

        return back()->with('bitcoin_success', 'Bitcoin Address & keys updated successfully!');
    }

    /**
     * POST: update Bitcoin Address & Keys
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postEthSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'eth_address' => 'bail|required|max:255',
            'eth_priv_key' => 'bail|required|max:255',
        ]);


        Auth::user()->update($validator->safe()->all());

        return back()->with('eth_success', 'Ethereum Address & keys updated successfully!');
    }
    /**
     * POST: update Solana Address & Keys
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postSolanaSetting(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'solana_address' => 'bail|required|max:255',
            'solana_priv_key' => 'bail|required|max:255',
        ]);


        Auth::user()->update($validator->safe()->all());

        return back()->with('solana_success', 'Solana Address & keys updated successfully!');
    }
}
