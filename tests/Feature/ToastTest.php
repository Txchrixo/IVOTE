<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ToastTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public function test_session_messages_are_rendered_as_toasts(): void
    {
        $this->actingAs(User::factory()->ea()->create())
            ->followingRedirects()
            ->post('/ea/addElection', ['title' => 'City Council', 'number' => 50])
            ->assertOk()
            ->assertSee('class="ui-toast ui-toast-success"', false)
            ->assertSee('role="status"', false)
            ->assertSee('Election added successfully!')
            ->assertDontSee('alert alert-success', false);
    }

    public function test_reaching_the_dashboard_shows_no_login_message(): void
    {
        $this->actingAs(User::factory()->ea()->create())
            ->followingRedirects()
            ->get('/auth/dashboard')
            ->assertOk()
            ->assertDontSee('You are logged in')
            ->assertDontSee('data-toast', false);
    }
}
