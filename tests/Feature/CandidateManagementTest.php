<?php

namespace Tests\Feature;

use App\Models\Candidate;
use App\Models\User;
use App\Models\VoteList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class CandidateManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $ra;
    private VoteList $election;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->ra = User::factory()->ra()->create();
        $this->election = VoteList::create(['title' => 'Presidential 2023', 'number' => 100]);
    }

    private function candidate(): Candidate
    {
        return Candidate::create(['name' => 'Alice', 'des' => 'Program', 'cni' => 'AB123456', 'vote_id' => $this->election->id]);
    }

    public function test_ra_registers_a_candidate_with_a_photo(): void
    {
        Storage::fake('public');

        $this->actingAs($this->ra)->post('/ra/addcandidate', [
            'name' => 'Alice',
            'cni' => 'AB123456',
            'des' => 'Program',
            'vote_id' => $this->election->id,
            'photo' => UploadedFile::fake()->image('alice.jpg'),
        ])->assertRedirect(route('ra.addCandidate'));

        $candidate = Candidate::sole();
        $this->assertSame('AB123456', $candidate->cni);
        Storage::disk('public')->assertExists('candidates/' . $candidate->photo);
    }

    public function test_ea_cannot_register_candidates(): void
    {
        $this->actingAs(User::factory()->ea()->create())->post('/ra/addcandidate', [
            'name' => 'Alice', 'cni' => 'AB123456', 'des' => 'Program', 'vote_id' => $this->election->id,
        ])->assertForbidden();
    }

    public function test_public_listing_hides_identity_numbers(): void
    {
        $this->candidate();

        $this->getJson('/api/candidates?vote_id=' . $this->election->id)
            ->assertOk()
            ->assertJsonPath('data.0.name', 'Alice')
            ->assertJsonMissingPath('data.0.cni');
    }

    public function test_ra_updates_and_deletes_candidates(): void
    {
        $candidate = $this->candidate();

        $this->actingAs($this->ra)
            ->putJson('/api/updatecandidate', ['id' => $candidate->id, 'name' => 'Alicia', 'des' => 'New program'])
            ->assertOk();
        $this->assertSame('Alicia', $candidate->fresh()->name);

        $this->actingAs($this->ra)
            ->deleteJson('/api/delcandidate', ['id' => $candidate->id])
            ->assertOk();
        $this->assertModelMissing($candidate);
    }

    public function test_invalid_candidate_id_returns_validation_error(): void
    {
        $this->actingAs($this->ra)
            ->deleteJson('/api/delcandidate', ['id' => 999])
            ->assertStatus(422)
            ->assertJsonPath('success', false);
    }
}
