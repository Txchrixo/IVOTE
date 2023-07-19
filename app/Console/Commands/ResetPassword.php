<?php

namespace App\Console\Commands;

use App\Models\User;
use App\Support\Audit;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rules\Password;

/**
 * Account recovery from the server shell: sets a new password (typed hidden)
 * and resets the second factor, which must be enrolled again at next login.
 * Existing sessions stop working because the password hash changes.
 */
class ResetPassword extends Command
{
    protected $signature = 'ivote:reset-password {username : Account to recover}';

    protected $description = 'Set a new password for an account and reset its two-factor authentication';

    public function handle(): int
    {
        $user = User::where('username', $this->argument('username'))->first();

        if (!$user) {
            $this->error('Unknown account.');
            return self::FAILURE;
        }

        $password = (string) $this->secret('New password');
        $confirmation = (string) $this->secret('Confirm the new password');

        $validator = Validator::make(
            ['password' => $password, 'password_confirmation' => $confirmation],
            ['password' => ['required', 'confirmed', 'max:255', Password::defaults()]]
        );

        if ($validator->fails()) {
            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }
            return self::FAILURE;
        }

        $user->forceFill([
            'password' => Hash::make($password),
            'two_factor_secret' => null,
            'two_factor_recovery_codes' => null,
            'two_factor_confirmed_at' => null,
            'two_factor_last_timestep' => null,
        ])->save();

        Audit::record('user.password_reset_cli', $user, ['two_factor_reset' => true]);

        $this->info("Password updated for '{$user->username}'. Two-factor authentication must be set up again at next login.");

        return self::SUCCESS;
    }
}
