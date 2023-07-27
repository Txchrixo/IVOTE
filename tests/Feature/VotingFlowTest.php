<?php

namespace Tests\Feature;

use App\Actions\AdvanceElectionStage;
use App\Enums\ElectionStage;
use App\Models\Code;
use App\Models\User;
use App\Models\VoteList;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Http;
use Symfony\Component\Process\Process;
use Tests\TestCase;

class VotingFlowTest extends TestCase
{
    use RefreshDatabase;

    protected function setUp(): void
    {
        parent::setUp();
        $this->withoutVite();
        config(['services.veriff.api_key' => null, 'services.bitcoin.rpc_url' => null]);
    }

    public function test_an_election_can_issue_codes_register_voters_and_count_signed_ballots(): void
    {
        $election = $this->election();
        $advance = app(AdvanceElectionStage::class);
        $this->actingAs(User::factory()->ra()->create())
            ->post(route('ra.ballot'), ['number' => 2, 'election_id' => $election->id])
            ->assertRedirect(route('ra.ballot'));
        $codes = Code::where('item_id', $election->id)->orderBy('id')->pluck('code');
        $this->assertCount(2, $codes);

        $advance($election);
        $voters = [];
        foreach ($codes as $code) {
            $voter = $this->voter($election, $code);
            $this->post(route('voter.register', $election), [
                'code' => $code, 'public_key' => $voter['publicKey'], 'proof' => $voter['proof'],
            ])->assertRedirect(route('voter.wait', ['election' => $election->id]));
            $voters[] = $voter;
        }
        $this->postJson(route('voter.register', $election), [
            'code' => $codes[0], 'public_key' => $voters[0]['publicKey'], 'proof' => $voters[0]['proof'],
        ])->assertUnprocessable()->assertJsonValidationErrors('code');

        $advance($election);
        $advance($election);
        $election->refresh();
        $definition = $election->ballot_definition;
        $first = $definition['candidates'][0]['id'];
        $second = $definition['candidates'][1]['id'];
        $endpoint = route('voter.ballots.submit', $election);

        $this->get(route('voter.vote', ['election' => $election->id]))
            ->assertOk()->assertSee('First candidate')->assertSee('Second candidate');
        $signed = $this->signBallot($definition, $first, $voters[0]['secretKey']);
        $this->postJson($endpoint, ['candidate' => $second, 'signature' => $signed])
            ->assertUnprocessable()->assertJsonValidationErrors('signature');
        $ballot = ['candidate' => $first, 'signature' => $signed];
        $receipt = $this->postJson($endpoint, $ballot)->assertCreated()->json('receipt');
        $this->postJson($endpoint, $ballot)->assertOk()->assertJsonPath('receipt', $receipt);
        $this->postJson($endpoint, [
            'candidate' => $second, 'signature' => $this->signBallot($definition, $second, $voters[0]['secretKey']),
        ])->assertConflict();

        $this->getJson(route('voter.ballots.ledger', $election))->assertNotFound();
        $advance($election);
        $this->postJson($endpoint, $ballot)->assertConflict();
        $this->assertDatabaseCount('ballots', 1);
        $this->get(route('tally.now', ['vote_item' => $election->id]))
            ->assertOk()->assertSeeInOrder(['First candidate', '<td>1</td>', 'Second candidate', '<td>0</td>'], false);
        $this->getJson(route('voter.ballots.receipt', [$election, $receipt]))
            ->assertOk()->assertJsonPath('candidate', $first)->assertJsonPath('signature', $signed);
        $this->getJson(route('voter.ballots.ledger', $election))
            ->assertOk()->assertJsonCount(1, 'ballots')->assertJsonMissingPath('ballots.0.bitcoin_transaction');
    }

    public function test_veriff_approval_must_match_the_signed_decision_for_the_issued_code(): void
    {
        $election = $this->election();
        app(AdvanceElectionStage::class)($election);
        $code = Code::create(['item_id' => $election->id, 'code' => 'LOCALTESTCODE']);
        $voter = $this->voter($election, $code->code);
        $registration = ['code' => $code->code, 'public_key' => $voter['publicKey'], 'proof' => $voter['proof']];
        config(['services.veriff.api_key' => 'fixture-key', 'services.veriff.secret' => 'fixture-secret']);
        $sessionId = '2bf528f7-4b9c-44f0-b928-fdc7afc5ca1b';
        Http::fake(['*/v1/sessions' => Http::response([
            'verification' => ['id' => $sessionId, 'url' => 'https://veriff.example/session'],
        ], 201)]);
        $this->postJson(route('voter.register', $election), $registration)->assertUnprocessable();
        $this->post(route('voter.identity', $election), ['code' => $code->code])
            ->assertRedirect('https://veriff.example/session');

        $decision = ['verification' => [
            'id' => $sessionId, 'vendorData' => $code->fresh()->verification_reference, 'status' => 'approved',
        ]];
        $body = json_encode($decision, JSON_THROW_ON_ERROR);
        $headers = [
            'CONTENT_TYPE' => 'application/json', 'HTTP_ACCEPT' => 'application/json',
            'HTTP_X_AUTH_CLIENT' => 'fixture-key',
        ];
        $this->call('POST', '/api/veriff/decision', [], [], [], $headers, $body)->assertUnauthorized();
        $this->call('POST', '/api/veriff/decision', [], [], [], $headers + [
            'HTTP_X_HMAC_SIGNATURE' => hash_hmac('sha256', $body, 'fixture-secret'),
        ], $body)->assertNoContent();
        $this->post(route('voter.register', $election), $registration)->assertRedirect(route('voter.wait', ['election' => $election->id]));
    }

    private function election(): VoteList
    {
        $election = VoteList::create([
            'title' => 'Demo election', 'number' => 4, 'stage' => ElectionStage::Initial->value,
            'start_veriff' => now()->addDay(), 'end_veriff' => now()->addDays(2),
            'start_vote' => now()->addDays(3), 'end_vote' => now()->addDays(4),
        ]);
        foreach (['First candidate', 'Second candidate'] as $name) {
            $election->candidates()->create(['name' => $name, 'des' => 'Demo candidate']);
        }

        return $election;
    }

    private function voter(VoteList $election, string $code): array
    {
        return $this->runNode(<<<'JS'
            const keys = generateKeyPair();
            const proof = secp256k1.sign(registrationDigest(input.election, input.code), keys.secretKey).toCompactHex();
            console.log(JSON.stringify({ ...keys, proof }));
            JS, ['election' => $election->id, 'code' => $code]);
    }

    private function signBallot(array $definition, int $candidate, string $secretKey): array
    {
        return $this->runNode(<<<'JS'
            const { reference, candidate, ring, secretKey } = input;
            console.log(JSON.stringify(sign(ballotMessage(reference, candidate), ring, secretKey)));
            JS, $definition + ['candidate' => $candidate, 'secretKey' => $secretKey]);
    }

    private function runNode(string $code, array $input): array
    {
        $script = <<<'JS'
            import { secp256k1 } from '@noble/curves/secp256k1';
            import { ballotMessage, registrationDigest } from './resources/js/crypto/ballot.mjs';
            import { generateKeyPair, sign } from './resources/js/crypto/lsag.js';
            let buffer = '';
            for await (const chunk of process.stdin) buffer += chunk;
            const input = JSON.parse(buffer);
            JS;
        $process = new Process(['node', '--input-type=module', '-e', $script . "\n" . $code], base_path());
        $process->setInput(json_encode($input, JSON_THROW_ON_ERROR))->mustRun();

        return json_decode($process->getOutput(), true, flags: JSON_THROW_ON_ERROR);
    }
}
