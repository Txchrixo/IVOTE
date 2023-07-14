<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SeederTest extends TestCase
{
    use RefreshDatabase;

    public function test_seeder_creates_authority_accounts_once(): void
    {
        $this->seed();
        $this->seed();

        $this->assertSame(Role::EA, User::where('username', 'ea')->sole()->role);
        $this->assertSame(Role::RA, User::where('username', 'ra')->sole()->role);
        $this->assertSame(2, User::count());
    }
}
