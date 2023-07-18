<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PragmaRX\Google2FA\Google2FA;
use Tests\TestCase;

class TwoFactorAuthenticationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    private function code(string $secret): string
    {
        return app(Google2FA::class)->getCurrentOtp($secret);
    }

    public function test_back_office_login_requires_the_second_factor(): void
    {
        $user = User::factory()->ea()->create(['username' => 'authority']);

        $this->post('/auth/login', ['username' => 'authority', 'password' => 'password'])
            ->assertRedirect(route('auth.two-factor.challenge'));
        $this->assertGuest();

        $this->post('/auth/two-factor/challenge', ['code' => '000000'])->assertSessionHasErrors('code');
        $this->assertGuest();

        $this->post('/auth/two-factor/challenge', ['code' => $this->code($user->two_factor_secret)])
            ->assertRedirect('auth/dashboard');
        $this->assertAuthenticatedAs($user);
    }

    public function test_a_code_cannot_be_replayed(): void
    {
        $user = User::factory()->ra()->create(['username' => 'registrar']);
        $code = $this->code($user->two_factor_secret);

        $this->post('/auth/login', ['username' => 'registrar', 'password' => 'password']);
        $this->post('/auth/two-factor/challenge', ['code' => $code]);
        $this->assertAuthenticatedAs($user);

        $this->post('/auth/logout', ['_method' => 'DELETE']);
        $this->assertGuest();

        $this->post('/auth/login', ['username' => 'registrar', 'password' => 'password']);
        $this->post('/auth/two-factor/challenge', ['code' => $code])->assertSessionHasErrors('code');
        $this->assertGuest();
    }

    public function test_recovery_codes_work_once(): void
    {
        $user = User::factory()->ea()->create(['username' => 'authority']);

        $this->post('/auth/login', ['username' => 'authority', 'password' => 'password']);
        $this->post('/auth/two-factor/challenge', ['recovery_code' => 'recov-ery01']);
        $this->assertAuthenticatedAs($user);
        $this->assertSame([], $user->fresh()->two_factor_recovery_codes);
    }

    public function test_accounts_without_second_factor_must_enroll_before_login(): void
    {
        $user = User::factory()->ea()->withoutTwoFactor()->create(['username' => 'authority']);

        $this->post('/auth/login', ['username' => 'authority', 'password' => 'password'])
            ->assertRedirect(route('auth.two-factor.setup'));
        $this->assertGuest();

        $this->get('/auth/two-factor/setup')->assertOk()->assertSee('<svg', false);
        $secret = session('login.two_factor_secret');

        $this->post('/auth/two-factor/setup', ['code' => '000000'])->assertSessionHasErrors('code');
        $this->assertGuest();

        $this->post('/auth/two-factor/setup', ['code' => $this->code($secret)])
            ->assertRedirect(route('auth.two-factor.recovery-codes'));
        $this->assertAuthenticatedAs($user);
        $this->assertTrue($user->fresh()->hasTwoFactorEnabled());
        $this->assertCount(8, session('two_factor_recovery_codes'));
    }

    public function test_challenge_without_pending_login_is_refused(): void
    {
        $this->post('/auth/two-factor/challenge', ['code' => '123456'])->assertRedirect(route('auth.login'));
        $this->assertGuest();
    }

    public function test_sessions_of_accounts_without_second_factor_are_closed(): void
    {
        $user = User::factory()->ea()->withoutTwoFactor()->create();

        $this->actingAs($user)->get('/ea/election')->assertRedirect(route('auth.login'));
        $this->assertGuest();
    }

    public function test_secret_is_encrypted_at_rest(): void
    {
        $user = User::factory()->ea()->create();

        $this->assertNotSame($user->two_factor_secret, $user->getRawOriginal('two_factor_secret'));
        $this->assertArrayNotHasKey('two_factor_secret', $user->toArray());
    }
}
