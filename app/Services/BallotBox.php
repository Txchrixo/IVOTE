<?php

namespace App\Services;

use App\Enums\ElectionStage;
use App\Models\Ballot;
use App\Models\Code;
use App\Models\VoteList;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BallotBox
{
    public function definition(VoteList $election): array
    {
        return $election->ballot_definition ?? [
            'reference' => hash_hmac('sha256', 'ivote/election/' . $election->id, (string) config('app.key')),
            'ring' => Code::where('item_id', $election->id)
                ->whereNotNull('registered_key')->orderBy('registered_key')->limit(129)->pluck('registered_key')->all(),
            'candidates' => $election->candidates()->orderBy('id')->get(['id', 'name'])->toArray(),
            'bitcoin' => (bool) config('services.bitcoin.rpc_url'),
        ];
    }

    public function submit(VoteList $election, int $candidate, array $signature, BallotCryptography $crypto): Ballot
    {
        return DB::transaction(function () use ($election, $candidate, $signature, $crypto) {
            $election = VoteList::whereKey($election->id)->lockForUpdate()->firstOrFail();
            abort_unless($election->currentStage() === ElectionStage::Ongoing, 409, 'Voting is closed.');

            $definition = $this->definition($election);
            $ring = $definition['ring'];
            if (count($ring) < 2 || count($ring) > 128) {
                abort(409, 'This election does not have a supported voter ring.');
            }
            if (!in_array($candidate, array_column($definition['candidates'], 'id'), true)) {
                throw ValidationException::withMessages(['candidate' => 'Choose a candidate in this election.']);
            }

            $receipt = $crypto->run([
                'reference' => $definition['reference'], 'candidate' => $candidate, 'ring' => $ring, 'signature' => $signature,
            ]);
            if (!preg_match('/\A[a-f0-9]{64}\z/', $receipt)) {
                throw ValidationException::withMessages(['signature' => 'The ballot signature is invalid.']);
            }

            $prior = $election->ballots()->where('key_image', $signature['keyImage'])->first();
            if ($prior) {
                abort_unless(hash_equals($prior->receipt, $receipt), 409, 'A ballot from this key is already recorded.');
                return $prior;
            }

            if (!$election->ballot_definition) {
                $election->forceFill(['ballot_definition' => $definition])->save();
            }

            $ballot = new Ballot();
            $ballot->forceFill([
                'candidate_id' => $candidate, 'key_image' => $signature['keyImage'],
                'signature' => $signature, 'receipt' => $receipt,
            ]);
            return $election->ballots()->save($ballot);
        });
    }
}
