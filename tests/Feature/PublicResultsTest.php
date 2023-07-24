<?php

namespace Tests\Feature;

use App\Enums\ElectionStage;
use App\Models\VoteList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicResultsTest extends TestCase
{
    use RefreshDatabase;

    private VoteList $closed;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->closed = VoteList::create(['title' => 'Student Union 2023', 'number' => 10, 'stage' => ElectionStage::Done->value]);
        VoteList::create(['title' => 'Club Board 2023', 'number' => 10, 'stage' => ElectionStage::Ongoing->value]);
    }

    public function test_home_pages_list_closed_elections_and_submit_to_their_page(): void
    {
        $this->get('/tally/home')->assertOk()
            ->assertSee(route('tally.now'), false)
            ->assertSee('Student union 2023')
            ->assertDontSee('Club board 2023');

        $this->get('/verify/home')->assertOk()
            ->assertSee(route('verify.now'), false)
            ->assertSee('Student union 2023')
            ->assertDontSee('Club board 2023');
    }

    public function test_election_pages_render(): void
    {
        $this->get('/tally/now?vote_item=' . $this->closed->id)->assertOk()->assertSee('Student union 2023');
        $this->get('/verify/now?vote_item=' . $this->closed->id)->assertOk()->assertSee('Student union 2023');
    }

    public function test_unknown_election_goes_back_to_the_list(): void
    {
        $this->get('/tally/now?vote_item=999')->assertRedirect(route('tally.home'));
        $this->get('/verify/now')->assertRedirect(route('verify.home'));
    }
}
