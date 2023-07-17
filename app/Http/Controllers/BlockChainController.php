<?php

namespace App\Http\Controllers;

use App\Support\Audit;
use App\Models\Key;
use App\Models\VoteList;
use BlockIo\Client as BlockIoClient;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;
use Illuminate\View\View;
use Throwable;

/**
 * Class BlockChainController
 * @author TxChrixo
 * Affiche les soldes des adresses et paie les frais de vote.
 * Tous les appels aux services tiers (Block.io, CryptoAPIs) se font ici, côté serveur :
 * les clés API et le PIN ne sont jamais envoyés au navigateur.
 * Toutes les routes de ce contrôleur sont protégées par le middleware EA.
 */
class BlockChainController extends Controller
{
    /** Format d'adresse accepté (base58 / bech32), pour éviter toute injection dans les URL. */
    private const ADDRESS_RULE = ['required', 'string', 'regex:/^[a-zA-Z0-9]{25,90}$/'];

    public function getBalanceList(): View
    {
        $result = VoteList::all();
        return view('ea.balance', ['data' => $result]);
    }

    public function getFeeList(): View
    {
        $result = VoteList::all();
        $user = Auth::user();
        return view('ea.fee', ['data' => $result, 'user' => $user]);
    }

    /**
     * Solde de l'adresse de l'EA via CryptoAPIs.
     * L'adresse vient du compte de l'EA connecté, jamais du client.
     */
    public function getEABalance(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'blockchain' => ['required', 'in:bitcoin'],
            'network' => ['required', 'in:testnet,mainnet'],
        ]);

        $address = Auth::user()->bitcoin_address;
        $apiKey = config('services.cryptoapis.api_key');

        if (!$address || !$apiKey) {
            return response()->json(['success' => false, 'message' => 'Unconfigured'], 422);
        }

        $url = sprintf(
            '%s/blockchain-data/%s/%s/addresses/%s/balance',
            rtrim(config('services.cryptoapis.base_url'), '/'),
            $validated['blockchain'],
            $validated['network'],
            rawurlencode($address)
        );

        try {
            $response = Http::timeout(10)->withHeaders(['x-api-key' => $apiKey])->acceptJson()->get($url);
        } catch (Throwable $e) {
            Log::warning('CryptoAPIs request failed', ['error' => $e->getMessage()]);
            return response()->json(['success' => false, 'message' => 'Service unavailable'], 502);
        }

        if (!$response->successful()) {
            return response()->json(['success' => false, 'message' => 'Service error'], 502);
        }

        return response()->json([
            'success' => true,
            'data' => [
                'amount' => data_get($response->json(), 'data.item.confirmedBalance.amount'),
                'unit' => data_get($response->json(), 'data.item.confirmedBalance.unit'),
                'network' => $validated['network'],
            ],
        ]);
    }

    public function getBlockIoBalance(): JsonResponse
    {
        try {
            return $this->ok($this->blockIo()->get_balance());
        } catch (Throwable $e) {
            return $this->blockIoError($e);
        }
    }

    public function getBlockIoAddressBalance(Request $request): JsonResponse
    {
        $validated = $request->validate(['address' => self::ADDRESS_RULE]);

        try {
            return $this->ok($this->blockIo()->get_address_balance(['addresses' => $validated['address']]));
        } catch (Throwable $e) {
            return $this->blockIoError($e);
        }
    }

    /**
     * Finance une adresse d'électeur (table `key`) avec le montant fixé en config.
     * Le client n'envoie que l'id de la clé : ni l'adresse ni le montant ne sont modifiables.
     * Le verrou empêche de payer deux fois la même adresse.
     */
    public function payVoterAddress(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'key_id' => ['required', 'integer', 'exists:key,id'],
        ]);

        return DB::transaction(function () use ($validated) {
            $key = Key::whereKey($validated['key_id'])->lockForUpdate()->firstOrFail();

            if ((int) $key->is_paid === 1) {
                return response()->json(['success' => false, 'message' => 'Already paid'], 409);
            }

            try {
                $client = $this->blockIo();
                $prepared = $client->prepare_transaction([
                    'to_address' => $key->bitcoin_address,
                    'amount' => (string) config('services.blockio.fee_amount'),
                ]);
                $signed = $client->create_and_sign_transaction($prepared);
                $result = $client->submit_transaction(['transaction_data' => $signed]);
            } catch (Throwable $e) {
                return $this->blockIoError($e);
            }

            $key->is_paid = 1;
            $key->save();

            Audit::record('wallet.voter_address_funded', $key, ['txid' => data_get($result, 'data.txid')]);

            return response()->json([
                'success' => true,
                'data' => ['txid' => data_get($result, 'data.txid')],
            ]);
        });
    }

    private function blockIo(): BlockIoClient
    {
        $apiKey = config('services.blockio.api_key');
        $pin = config('services.blockio.pin');

        if (!$apiKey || !$pin) {
            throw new \RuntimeException('Block.io is not configured');
        }

        return new BlockIoClient($apiKey, $pin, config('services.blockio.version', 2));
    }

    private function ok($result): JsonResponse
    {
        return response()->json(['success' => true, 'data' => $result, 'message' => 'OK']);
    }

    private function blockIoError(Throwable $e): JsonResponse
    {
        // Le détail reste dans les logs serveur, jamais renvoyé au client
        Log::warning('Block.io request failed', ['error' => $e->getMessage()]);
        return response()->json(['success' => false, 'message' => 'Block.io request failed'], 502);
    }
}
