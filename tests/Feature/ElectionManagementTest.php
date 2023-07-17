<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\VoteList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ElectionManagementTest extends TestCase
{
    use RefreshDatabase;

    private User $ea;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        $this->ea = User::factory()->ea()->create();
    }

    private function election(array $attributes = []): VoteList
    {
        return VoteList::create(array_merge(['title' => 'Presidential 2023', 'number' => 100], $attributes));
    }

    public function test_ea_creates_an_election(): void
    {
        $this->actingAs($this->ea)
            ->post('/ea/addElection', ['title' => 'City Council', 'number' => 50])
            ->assertRedirect(route('ea.election'));

        $this->assertDatabaseHas('vote', ['title' => 'city-council', 'number' => 50]);
    }

    public function test_duplicate_titles_are_rejected(): void
    {
        $this->election(['title' => 'City Council']);

        $this->actingAs($this->ea)
            ->post('/ea/addElection', ['title' => 'city council', 'number' => 50])
            ->assertSessionHasErrors('slug');
    }

    public function test_ra_cannot_create_an_election(): void
    {
        $this->actingAs(User::factory()->ra()->create())
            ->post('/ea/addElection', ['title' => 'City Council', 'number' => 50])
            ->assertForbidden();
    }

    public function test_update_rejects_badly_ordered_dates_and_ignores_stage(): void
    {
        $election = $this->election();

        $this->actingAs($this->ea)->putJson('/api/election/update', [
            'id' => $election->id,
            'title' => 'Presidential 2023',
            'number' => 100,
            'start_vote' => now()->addDays(5)->toDateTimeString(),
            'end_vote' => now()->addDays(4)->toDateTimeString(),
        ])->assertStatus(422)->assertJsonPath('success', false)->assertJsonStructure(['data' => ['end_vote']]);

        $this->actingAs($this->ea)->putJson('/api/election/update', [
            'id' => $election->id,
            'title' => 'Presidential 2023',
            'number' => 120,
            'stage' => 4,
        ])->assertOk();

        $this->assertSame(120, (int) $election->fresh()->number);
        $this->assertSame(0, (int) $election->fresh()->stage);
    }

    public function test_stage_change_requires_the_current_ea_password(): void
    {
        $election = $this->readyElection();

        $this->actingAs($this->ea)
            ->putJson('/api/election/toggleStage', ['id' => $election->id, 'password' => 'wrong'])
            ->assertStatus(422)
            ->assertJsonStructure(['data' => ['password']]);

        $this->actingAs($this->ea)
            ->putJson('/api/election/toggleStage', ['id' => $election->id, 'password' => 'password'])
            ->assertOk()
            ->assertJsonPath('data.stage', 1);

        $this->assertDatabaseHas('audit_logs', [
            'action' => 'election.stage_changed',
            'subject_id' => $election->id,
            'user_id' => $this->ea->id,
        ]);
    }

    public function test_stage_transitions_have_preconditions(): void
    {
        $election = $this->election();

        $this->actingAs($this->ea)
            ->putJson('/api/election/toggleStage', ['id' => $election->id, 'password' => 'password'])
            ->assertStatus(422)
            ->assertJsonPath('data', 'Set the identity verification dates first.');

        $election = $this->readyElection(['title' => 'Other election']);
        $election->forceFill(['stage' => 2])->save();

        $this->actingAs($this->ea)
            ->putJson('/api/election/toggleStage', ['id' => $election->id, 'password' => 'password'])
            ->assertStatus(422)
            ->assertJsonPath('data', 'Set the voting dates first.');

        $election->forceFill(['stage' => 4])->save();
        $this->actingAs($this->ea)
            ->putJson('/api/election/toggleStage', ['id' => $election->id, 'password' => 'password'])
            ->assertStatus(422)
            ->assertJsonPath('data', 'The election is already closed.');
    }

    private function readyElection(array $attributes = []): VoteList
    {
        $election = $this->election(array_merge([
            'start_veriff' => now()->addDay(),
            'end_veriff' => now()->addDays(2),
        ], $attributes));
        $election->candidates()->createMany([
            ['name' => 'Alice', 'des' => 'A'],
            ['name' => 'Bob', 'des' => 'B'],
        ]);

        return $election;
    }

    public function test_background_upload_only_accepts_real_images(): void
    {
        Storage::fake('public');
        $election = $this->election();

        $this->actingAs($this->ea)->post('/ea/election/upload/bg-image', [
            'election' => $election->id,
            'file' => UploadedFile::fake()->createWithContent('bg.png', '<svg onload="alert(1)"></svg>'),
        ])->assertSessionHasErrors('file');

        $this->actingAs($this->ea)->post('/ea/election/upload/bg-image', [
            'election' => $election->id,
            'file' => UploadedFile::fake()->image('bg.png', 800, 600),
        ])->assertSessionHasNoErrors();

        $stored = $election->fresh()->bg;
        $this->assertStringEndsWith('.png', $stored);
        Storage::disk('public')->assertExists('uploads/bg/' . $stored);
    }
}
