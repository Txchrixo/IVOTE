<?php

namespace App\Http\Controllers\EA;

use App\Http\Controllers\Controller;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;

/**
 * Class EASettingController
 * @author TxChrixo
 * Réglages des adresses et clés de l'EA.
 * Les clés privées ne sont jamais renvoyées au navigateur : le champ reste vide
 * et n'écrase la clé existante que s'il est rempli. Elles sont chiffrées au repos (cast `encrypted`).
 * @package Controllers\EA
 */
class EASettingController extends Controller
{
    public function getSetting(): View
    {
        return view('ea.setting', ['user' => Auth::user()]);
    }

    public function postBitcoinSetting(Request $request): RedirectResponse
    {
        $this->saveChain($request, 'bitcoin');
        return back()->with('bitcoin_success', 'Bitcoin Address & keys updated successfully!');
    }

    public function postEthSetting(Request $request): RedirectResponse
    {
        $this->saveChain($request, 'eth');
        return back()->with('eth_success', 'Ethereum Address & keys updated successfully!');
    }

    public function postSolanaSetting(Request $request): RedirectResponse
    {
        $this->saveChain($request, 'solana');
        return back()->with('solana_success', 'Solana Address & keys updated successfully!');
    }

    private function saveChain(Request $request, string $chain): void
    {
        $address = $chain . '_address';
        $privKey = $chain . '_priv_key';

        $validated = $request->validate([
            $address => ['bail', 'required', 'string', 'regex:/^[a-zA-Z0-9]{25,90}$/'],
            $privKey => ['bail', 'nullable', 'string', 'max:255', 'regex:/^[a-zA-Z0-9]+$/'],
        ]);

        $data = [$address => $validated[$address]];
        if (!empty($validated[$privKey])) {
            $data[$privKey] = $validated[$privKey];
        }

        Auth::user()->update($data);

        Log::info('EA wallet settings updated', [
            'user_id' => Auth::id(),
            'chain' => $chain,
            'private_key_changed' => isset($data[$privKey]),
        ]);
    }
}
