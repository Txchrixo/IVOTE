<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Tests\TestCase;

class BlockchainBalanceTest extends TestCase
{
    use RefreshDatabase;

    private User $ea;

    protected function setUp(): void
    {
        parent::setUp();
        config(['services.cryptoapis.api_key' => 'test-key', 'services.solana.rpc_url' => 'https://solana.test']);
        $this->ea = User::factory()->ea()->create([
            'bitcoin_address' => 'mhcuKmSoiiZzm7mtUtdaP32MJy237kSeUp',
            'eth_address' => '0x5a0b54d5dc17e0aadc383d2db43b0a0d3e029c4c',
            'solana_address' => '7xKXtg2CW87d97TXJSDpbD5jBkheTqA83TZRuJosgAsU',
        ]);
    }

    public function test_bitcoin_and_ethereum_balances_come_from_cryptoapis(): void
    {
        Http::fake([
            '*/bitcoin/testnet/addresses/mhcuKmSoiiZzm7mtUtdaP32MJy237kSeUp/balance' => Http::response(['data' => ['item' => ['confirmedBalance' => ['amount' => '0.0421']]]]),
            '*/ethereum/goerli/addresses/*' => Http::response(['data' => ['item' => ['confirmedBalance' => ['amount' => '1.25']]]]),
        ]);

        $this->actingAs($this->ea)->getJson('/ea/api/balance?blockchain=bitcoin')
            ->assertOk()->assertJsonPath('data.amount', '0.0421')->assertJsonPath('data.unit', 'tBTC');
        $this->actingAs($this->ea)->getJson('/ea/api/balance?blockchain=ethereum')
            ->assertOk()->assertJsonPath('data.amount', '1.25')->assertJsonPath('data.network', 'goerli');
    }

    public function test_solana_balance_comes_from_the_rpc_in_sol(): void
    {
        Http::fake(['solana.test' => Http::response(['result' => ['value' => 2_500_000_000]])]);

        $this->actingAs($this->ea)->getJson('/ea/api/balance?blockchain=solana')
            ->assertOk()->assertJsonPath('data.amount', '2.500000000')->assertJsonPath('data.unit', 'SOL');

        Http::assertSent(fn ($request) => $request['method'] === 'getBalance'
            && $request['params'] === ['7xKXtg2CW87d97TXJSDpbD5jBkheTqA83TZRuJosgAsU']);
    }

    public function test_missing_address_is_reported_as_unconfigured(): void
    {
        $this->ea->update(['solana_address' => null]);

        $this->actingAs($this->ea)->getJson('/ea/api/balance?blockchain=solana')->assertStatus(422);
        $this->actingAs($this->ea)->getJson('/ea/api/balance?blockchain=dogecoin')->assertStatus(422);
    }
}
