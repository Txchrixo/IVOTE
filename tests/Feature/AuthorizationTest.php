<?php

namespace Tests\Feature;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use PHPUnit\Framework\Attributes\DataProvider;
use Tests\TestCase;

class AuthorizationTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
    }

    public static function eaPages(): array
    {
        return [['/ea/election'], ['/ea/addElection'], ['/ea/balance'], ['/ea/fee'], ['/ea/setting']];
    }

    public static function raPages(): array
    {
        return [['/ra/candidates'], ['/ra/addcandidate'], ['/ra/addVoter'], ['/ra/ballot']];
    }

    #[DataProvider('eaPages')]
    public function test_ea_pages_are_reserved_to_the_election_authority(string $uri): void
    {
        $this->get($uri)->assertRedirect(route('auth.login'));
        $this->actingAs(User::factory()->ra()->create())->get($uri)->assertForbidden();
        $this->actingAs(User::factory()->create())->get($uri)->assertForbidden();
        $this->actingAs(User::factory()->ea()->create())->get($uri)->assertOk();
    }

    #[DataProvider('raPages')]
    public function test_ra_pages_are_reserved_to_the_registration_authority(string $uri): void
    {
        $this->get($uri)->assertRedirect(route('auth.login'));
        $this->actingAs(User::factory()->ea()->create())->get($uri)->assertForbidden();
        $this->actingAs(User::factory()->create())->get($uri)->assertForbidden();
        $this->actingAs(User::factory()->ra()->create())->get($uri)->assertOk();
    }

    public function test_role_cannot_be_mass_assigned(): void
    {
        $user = User::factory()->create();
        $user->update(['role' => 2]);

        $this->assertFalse($user->fresh()->isEA());
    }

    public function test_forbidden_page_offers_a_way_back(): void
    {
        $this->actingAs(User::factory()->ra()->create())
            ->get('/ea/election')
            ->assertForbidden()
            ->assertSee('Access denied')
            ->assertSee(route('auth.dashboard'), false);
    }

    public function test_back_office_layout_has_profile_menu_with_role(): void
    {
        $user = User::factory()->ea()->create(['username' => 'authority', 'nickname' => 'Jane Doe']);

        $this->actingAs($user)->get('/ea/election')
            ->assertOk()
            ->assertSeeInOrder(['data-sidebar-toggle', 'data-dropdown', 'app-profile'], false)
            ->assertSee('Jane Doe')
            ->assertSee('Election authority')
            ->assertSee('aria-current="page"', false);
    }
}
