<?php

namespace Tests\Feature;

use App\Models\Code;
use App\Models\VoteList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class PublicKeysTest extends TestCase
{
    use RefreshDatabase;

    public function test_lists_public_keys_without_revealing_codes(): void
    {
        $election = VoteList::create(['title' => 'Presidential 2023', 'number' => 10]);
        Code::create(['code' => 'CODE1', 'public_key' => 'KEY1', 'item_id' => $election->id]);
        Code::create(['code' => 'CODE2', 'public_key' => 'KEY2', 'item_id' => $election->id]);
        Code::create(['code' => 'CODE3', 'public_key' => null, 'item_id' => $election->id]);

        $this->getJson("/api/getAllPubKeys?election_id={$election->id}")
            ->assertOk()
            ->assertJsonCount(2, 'data')
            ->assertJsonMissing(['code' => 'CODE1']);

        $this->getJson("/api/getAllPubKeys?election_id={$election->id}&code=CODE1")
            ->assertOk()
            ->assertExactJson(['success' => true, 'data' => [['public_key' => 'KEY2']], 'message' => 'Public keys retrieved!']);

        // An unknown code gives the same kind of answer as a valid one
        $this->getJson("/api/getAllPubKeys?election_id={$election->id}&code=UNKNOWN")
            ->assertOk()
            ->assertJsonCount(2, 'data');
    }
}
