<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class PasswordChangeTest extends TestCase
{
    use RefreshDatabase;

    public function test_password_change_requires_current_password_and_strong_new_one(): void
    {
        $user = User::factory()->ea()->create();

        $this->actingAs($user)->post('/auth/password/change', [
            'password_old' => 'wrong',
            'password' => 'N3w-Strong-Passw0rd!',
            'password_confirmation' => 'N3w-Strong-Passw0rd!',
        ])->assertSessionHasErrors('password_old');

        $this->actingAs($user)->post('/auth/password/change', [
            'password_old' => 'password',
            'password' => 'short',
            'password_confirmation' => 'short',
        ])->assertSessionHasErrors('password');

        $this->actingAs($user)->post('/auth/password/change', [
            'password_old' => 'password',
            'password' => 'N3w-Strong-Passw0rd!',
            'password_confirmation' => 'N3w-Strong-Passw0rd!',
        ])->assertSessionHasNoErrors()->assertRedirect(route('auth.password.change'));

        $this->assertTrue(Hash::check('N3w-Strong-Passw0rd!', $user->fresh()->password));
    }

    public function test_guests_cannot_change_passwords(): void
    {
        $this->post('/auth/password/change', [])->assertRedirect(route('auth.login'));
    }
}
