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

    private const ATTEMPTS = 3;

    protected $description = 'Set a new password for an account and reset its two-factor authentication';

    public function handle(): int
    {
        $user = User::where('username', $this->argument('username'))->first();

        if (!$user) {
            $this->error('Unknown account.');
            return self::FAILURE;
        }

        $this->line('The password needs at least 12 characters with upper and lower case letters, a number and a symbol.');
        $this->line('Nothing is displayed while you type.');

        $password = null;
        for ($attempt = 1; $attempt <= self::ATTEMPTS && $password === null; $attempt++) {
            $candidate = (string) $this->secret('New password');
            $confirmation = (string) $this->secret('Confirm the new password');

            $validator = Validator::make(
                ['password' => $candidate, 'password_confirmation' => $confirmation],
                ['password' => ['required', 'confirmed', 'max:255', Password::defaults()]]
            );

            if ($validator->passes()) {
                $password = $candidate;
                break;
            }

            foreach ($validator->errors()->all() as $message) {
                $this->error($message);
            }
            if ($attempt < self::ATTEMPTS) {
                $this->warn('Please try again.');
            }
        }

        if ($password === null) {
            $this->error('Password not changed.');
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
