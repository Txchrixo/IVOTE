<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Support\Facades\File;
use Tests\TestCase;

/**
 * Regression tests for the security fixes.
 * These tests do not need a database.
 */
class SecurityRegressionTest extends TestCase
{
    private function user(int $role): User
    {
        $user = new User(['username' => 'test', 'role' => $role]);
        $user->id = 999;
        $user->exists = true;
        return $user;
    }

    public function test_encryption_oracle_endpoints_are_gone(): void
    {
        $this->get('/decrypt-data?encryptedData=x')->assertNotFound();
        $this->get('/encrypt-data?data=a@b.c')->assertNotFound();
    }

    public function test_open_cryptoapis_proxy_is_gone(): void
    {
        $this->get('/cryptoapis-proxy/blockchain-data/bitcoin/testnet/addresses/x/balance')->assertNotFound();
    }

    public function test_unauthenticated_veriff_params_endpoint_is_gone(): void
    {
        $this->post('/postVeriffParams')->assertStatus(404);
    }

    public function test_blockchain_endpoints_require_ea(): void
    {
        foreach (['/ea/api/balance', '/api/blockio/get_balance', '/api/blockio/get_address_balance', '/api/allbitcoinaddress', '/api/election/details'] as $uri) {
            $this->get($uri)->assertRedirect(route('auth.login'));
            $this->actingAs($this->user(Role::$RA))->get($uri)->assertRedirect(route('auth.login'));
            auth()->logout();
        }
        $this->post('/ea/api/blockio/pay')->assertRedirect(route('auth.login'));
    }

    public function test_register_is_reserved_to_ea(): void
    {
        $this->get('/auth/register')->assertRedirect(route('auth.login'));
        $this->post('/auth/register', ['username' => 'x', 'password' => 'x'])->assertRedirect(route('auth.login'));
        $this->actingAs($this->user(Role::$RA))->get('/auth/register')->assertRedirect(route('auth.login'));
    }

    public function test_wildcard_cors_is_disabled(): void
    {
        $this->withHeaders(['Origin' => 'https://evil.example'])
            ->get('/auth/login')
            ->assertHeaderMissing('Access-Control-Allow-Origin');

        $this->withHeaders(['Origin' => 'https://evil.example'])
            ->get('/api/candidates')
            ->assertHeaderMissing('Access-Control-Allow-Origin');
    }

    public function test_login_is_rate_limited(): void
    {
        for ($i = 0; $i < 5; $i++) {
            $this->post('/auth/login', ['username' => 'nobody', 'password' => ''])->assertStatus(302);
        }
        $this->post('/auth/login', ['username' => 'nobody', 'password' => ''])->assertStatus(429);
    }

    public function test_private_keys_are_encrypted_and_hidden(): void
    {
        $user = new User(['username' => 'ea', 'bitcoin_priv_key' => 'cTestWifKey123']);

        $this->assertNotSame('cTestWifKey123', $user->getAttributes()['bitcoin_priv_key']);
        $this->assertSame('cTestWifKey123', $user->bitcoin_priv_key);
        $this->assertArrayNotHasKey('bitcoin_priv_key', $user->toArray());
    }

    /**
     * Les valeurs des clés sont lues depuis la config (.env) : aucun secret n'est écrit dans ce test.
     */
    public function test_no_hardcoded_secrets_in_source(): void
    {
        $secrets = array_filter([
            config('services.cryptoapis.api_key'),
            config('services.blockio.api_key'),
            config('services.blockio.pin'),
            config('services.veriff.api_key'),
        ]);

        if ($secrets === []) {
            $this->markTestSkipped('No service keys configured in .env');
        }

        $files = collect(['app', 'routes', 'config', 'resources/js', 'resources/views', 'tests'])
            ->flatMap(fn ($dir) => File::allFiles(base_path($dir)));

        foreach ($files as $file) {
            $content = $file->getContents();
            foreach ($secrets as $secret) {
                $this->assertStringNotContainsString($secret, $content, "Secret found in {$file->getRelativePathname()}");
            }
        }
    }
}
