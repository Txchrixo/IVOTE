<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class ResetPasswordCommandTest extends TestCase
{
    use RefreshDatabase;

    public function test_resets_password_and_second_factor(): void
    {
        $user = User::factory()->ea()->create(['username' => 'authority']);

        $this->artisan('ivote:reset-password', ['username' => 'authority'])
            ->expectsQuestion('New password', 'N3w-Strong-Passw0rd!')
            ->expectsQuestion('Confirm the new password', 'N3w-Strong-Passw0rd!')
            ->assertSuccessful();

        $user->refresh();
        $this->assertTrue(Hash::check('N3w-Strong-Passw0rd!', $user->password));
        $this->assertFalse($user->hasTwoFactorEnabled());
        $this->assertDatabaseHas('audit_logs', ['action' => 'user.password_reset_cli', 'subject_id' => $user->id]);
    }

    public function test_lets_the_user_retry_after_an_invalid_password(): void
    {
        $user = User::factory()->ea()->create(['username' => 'authority']);

        $this->artisan('ivote:reset-password', ['username' => 'authority'])
            ->expectsQuestion('New password', 'weak')
            ->expectsQuestion('Confirm the new password', 'weak')
            ->expectsQuestion('New password', 'N3w-Strong-Passw0rd!')
            ->expectsQuestion('Confirm the new password', 'N3w-Strong-Passw0rd!')
            ->assertSuccessful();

        $this->assertTrue(Hash::check('N3w-Strong-Passw0rd!', $user->fresh()->password));
    }

    public function test_gives_up_after_three_invalid_attempts(): void
    {
        $user = User::factory()->ea()->create(['username' => 'authority']);
        $hash = $user->password;

        $command = $this->artisan('ivote:reset-password', ['username' => 'authority']);
        foreach (range(1, 3) as $attempt) {
            $command->expectsQuestion('New password', 'N3w-Strong-Passw0rd!')
                ->expectsQuestion('Confirm the new password', 'Other-Strong-Passw0rd!');
        }
        $command->assertFailed();

        $this->assertSame($hash, $user->fresh()->password);
        $this->assertTrue($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_unknown_account_fails(): void
    {
        $this->artisan('ivote:reset-password', ['username' => 'nobody'])->assertFailed();
    }
}
