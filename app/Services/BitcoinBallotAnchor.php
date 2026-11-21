<?php

namespace App\Services;

use App\Models\Ballot;
use Illuminate\Http\Client\ConnectionException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

class BitcoinBallotAnchor
{
    public function checkNetwork(): void
    {
        $info = $this->rpc('getblockchaininfo');
        if (!in_array($info['chain'] ?? null, ['test', 'testnet4', 'regtest'], true)) {
            throw new RuntimeException('Use a Bitcoin Testnet3, Testnet4 or regtest wallet.');
        }
    }

    public function publish(Ballot $ballot): bool
    {
        return Cache::store('file')->lock('bitcoin-ballot-wallet', 90)->block(3, function () use ($ballot) {
            $ballot = DB::transaction(function () use ($ballot) {
                $locked = Ballot::whereKey($ballot->id)->lockForUpdate()->firstOrFail();
                if (!$locked->bitcoin_transaction) {
                    $this->prepare($locked);
                }
                return $locked;
            });

            $transaction = $this->findTransaction($ballot->bitcoin_txid);
            if (!$transaction) {
                $txid = $this->rpc('sendrawtransaction', [$ballot->bitcoin_transaction]);
                if ($txid !== $ballot->bitcoin_txid) {
                    throw new RuntimeException('Bitcoin Core returned a different transaction ID.');
                }
                $transaction = $this->findTransaction($txid);
            }

            if (!$transaction) {
                throw new RuntimeException('The Bitcoin transaction is not visible yet. Retry later.');
            }
            $this->checkCommitment($this->rpc('decoderawtransaction', [$transaction['hex']]), $ballot->receipt);
            $confirmed = ($transaction['confirmations'] ?? 0) > 0;
            $ballot->forceFill([
                'bitcoin_confirmed_at' => $confirmed ? ($ballot->bitcoin_confirmed_at ?? now()) : null,
            ])->save();

            return $confirmed;
        });
    }

    private function prepare(Ballot $ballot): void
    {
        $raw = $this->rpc('createrawtransaction', [[], [['data' => $ballot->receipt]], 0, false]);
        $funded = $this->rpc('fundrawtransaction', [$raw, ['fee_rate' => 2]]);
        $signed = $this->rpc('signrawtransactionwithwallet', [$funded['hex']]);
        if (!($signed['complete'] ?? false)) {
            throw new RuntimeException('Bitcoin Core could not sign the transaction.');
        }

        $decoded = $this->rpc('decoderawtransaction', [$signed['hex']]);
        $this->checkCommitment($decoded, $ballot->receipt);
        $ballot->forceFill(['bitcoin_txid' => $decoded['txid'], 'bitcoin_transaction' => $signed['hex']])->save();
    }

    private function findTransaction(string $txid): ?array
    {
        try {
            return $this->rpc('gettransaction', [$txid, false]);
        } catch (RuntimeException $exception) {
            if ($exception->getCode() === -5) {
                return null;
            }
            throw $exception;
        }
    }

    private function checkCommitment(array $transaction, string $receipt): void
    {
        $scripts = array_map(fn ($output) => $output['scriptPubKey']['hex'] ?? '', $transaction['vout'] ?? []);
        if (!in_array('6a20' . $receipt, $scripts, true)) {
            throw new RuntimeException('The Bitcoin transaction does not commit to this receipt.');
        }
    }

    private function rpc(string $method, array $parameters = []): mixed
    {
        $url = config('services.bitcoin.rpc_url');
        if (!$url) {
            throw new RuntimeException('Configure a Bitcoin Core wallet RPC URL.');
        }

        try {
            $response = Http::timeout(8)
                ->withBasicAuth((string) config('services.bitcoin.rpc_user'), (string) config('services.bitcoin.rpc_password'))
                ->post($url, ['jsonrpc' => '1.0', 'id' => 'ivote', 'method' => $method, 'params' => $parameters]);
        } catch (ConnectionException) {
            throw new RuntimeException('Could not connect to Bitcoin Core.');
        }

        if (!$response->successful() || $response->json('error')) {
            throw new RuntimeException('Bitcoin Core rejected ' . $method . '.', (int) $response->json('error.code'));
        }
        return $response->json('result');
    }
}
