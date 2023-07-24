<?php

namespace Tests\Feature;

use App\Enums\Role;
use App\Models\User;
use Database\Seeders\DemoSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use RuntimeException;
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

    public function test_demo_seeder_never_runs_in_production(): void
    {
        $this->app['env'] = 'production';

        try {
            (new DemoSeeder())->run();
            $this->fail('The demo seeder ran in production.');
        } catch (RuntimeException) {
            $this->assertSame(0, User::count());
        }
    }
}
