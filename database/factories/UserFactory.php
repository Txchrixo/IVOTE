<?php

namespace Database\Factories;

use App\Enums\Role;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use PragmaRX\Google2FA\Google2FA;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\User>
 */
class UserFactory extends Factory
{
    protected static ?string $password;

    public function definition(): array
    {
        return [
            'username' => fake()->unique()->userName(),
            'nickname' => fake()->firstName(),
            'password' => static::$password ??= Hash::make('password'),
            'role' => Role::VOTER,
            'remember_token' => Str::random(10),
        ];
    }

    /** Back-office accounts come with a confirmed second factor. */
    public function ea(): static
    {
        return $this->state(fn () => ['role' => Role::EA])->withTwoFactor();
    }

    public function ra(): static
    {
        return $this->state(fn () => ['role' => Role::RA])->withTwoFactor();
    }

    public function withTwoFactor(): static
    {
        return $this->state(fn () => [
            'two_factor_secret' => app(Google2FA::class)->generateSecretKey(32),
            'two_factor_recovery_codes' => [Hash::make('RECOV-ERY01')],
            'two_factor_confirmed_at' => now(),
        ]);
    }

    public function withoutTwoFactor(): static
    {
        return $this->state(fn () => [
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
        ]);
    }
}
