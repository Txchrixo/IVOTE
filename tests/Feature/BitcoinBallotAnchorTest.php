<?php

namespace Tests\Feature;

use App\Services\BitcoinBallotAnchor;
use Illuminate\Support\Facades\Http;
use RuntimeException;
use Tests\TestCase;

class BitcoinBallotAnchorTest extends TestCase
{
    public function test_public_testnet_four_is_accepted(): void
    {
        config(['services.bitcoin.rpc_url' => 'http://127.0.0.1:48332/wallet/ivote-test']);
        Http::fake(['*' => Http::response(['result' => ['chain' => 'testnet4'], 'error' => null])]);

        app(BitcoinBallotAnchor::class)->checkNetwork();

        Http::assertSent(fn ($request) => $request['method'] === 'getblockchaininfo');
    }

    public function test_mainnet_is_refused_before_any_wallet_operation(): void
    {
        config(['services.bitcoin.rpc_url' => 'http://127.0.0.1:48332/wallet/ivote-test']);
        Http::fake(['*' => Http::response(['result' => ['chain' => 'main'], 'error' => null])]);

        $this->expectException(RuntimeException::class);
        $this->expectExceptionMessage('Use a Bitcoin Testnet3, Testnet4 or regtest wallet.');

        app(BitcoinBallotAnchor::class)->checkNetwork();
    }
}
