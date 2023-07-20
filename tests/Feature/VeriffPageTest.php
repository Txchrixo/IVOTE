<?php

namespace Tests\Feature;

use App\Enums\ElectionStage;
use App\Models\VoteList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class VeriffPageTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        VoteList::create(['title' => 'Presidentielle 2023', 'number' => 10, 'stage' => ElectionStage::Veriff->value]);
        VoteList::create(['title' => 'Municipale 2023', 'number' => 10, 'stage' => ElectionStage::Initial->value]);
    }

    public function test_exact_election_in_verification_stage_is_shown(): void
    {
        $this->get('/veriff/election/presidentielle-2023')->assertOk();
    }

    public function test_close_match_redirects_to_the_canonical_url(): void
    {
        $this->get('/veriff/election/presidentielle')
            ->assertRedirect(route('voter.veriff', ['election' => 'presidentielle-2023']));
    }

    public function test_elections_outside_the_verification_stage_are_not_found(): void
    {
        $this->get('/veriff/election/municipale-2023')->assertNotFound();
        $this->get('/veriff/election/%25')->assertNotFound();
    }
}
