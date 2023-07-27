<?php

namespace Database\Seeders;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Add missing authority accounts. If a seed password is unset, print a generated password once.
     */
    public function run(): void
    {
        $this->account(Role::EA, env('SEED_EA_USERNAME', 'ea'), env('SEED_EA_PASSWORD'));
        $this->account(Role::RA, env('SEED_RA_USERNAME', 'ra'), env('SEED_RA_PASSWORD'));
    }

    private function account(Role $role, string $username, ?string $password): void
    {
        if (User::where('username', $username)->exists()) {
            $this->command?->info("{$role->label()} account '{$username}' already exists, skipped.");
            return;
        }

        $generated = blank($password);
        $password = $generated ? Str::password(20) : $password;

        $user = new User(['username' => $username, 'password' => Hash::make($password)]);
        $user->role = $role;
        $user->save();

        $this->command?->info("{$role->label()} account '{$username}' created.");
        if ($generated) {
            $this->command?->warn("Generated password for '{$username}': {$password}");
        }
    }
}
