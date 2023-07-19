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

    public function test_rejects_weak_or_mismatched_passwords(): void
    {
        $user = User::factory()->ea()->create(['username' => 'authority']);
        $hash = $user->password;

        $this->artisan('ivote:reset-password', ['username' => 'authority'])
            ->expectsQuestion('New password', 'weak')
            ->expectsQuestion('Confirm the new password', 'weak')
            ->assertFailed();

        $this->artisan('ivote:reset-password', ['username' => 'authority'])
            ->expectsQuestion('New password', 'N3w-Strong-Passw0rd!')
            ->expectsQuestion('Confirm the new password', 'Other-Strong-Passw0rd!')
            ->assertFailed();

        $this->assertSame($hash, $user->fresh()->password);
        $this->assertTrue($user->fresh()->hasTwoFactorEnabled());
    }

    public function test_unknown_account_fails(): void
    {
        $this->artisan('ivote:reset-password', ['username' => 'nobody'])->assertFailed();
    }
}
