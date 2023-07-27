<?php

namespace App\Http\Controllers\Voter;

use App\Enums\ElectionStage;
use App\Http\Controllers\Controller;
use App\Models\VoteList;
use App\Services\BallotBox;
use App\Services\BallotCryptography;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BallotController extends Controller
{
    public function submit(Request $request, VoteList $election, BallotBox $box, BallotCryptography $crypto): JsonResponse
    {
        abort_unless($request->isJson(), 415);
        $data = $request->validate([
            'candidate' => ['required', 'integer', 'min:1'],
            'signature' => ['required', 'array:c0,s,keyImage'],
            'signature.c0' => ['required', 'regex:/\A[a-f0-9]{64}\z/'],
            'signature.s' => ['required', 'array', 'min:2', 'max:128'],
            'signature.s.*' => ['required', 'regex:/\A[a-f0-9]{64}\z/'],
            'signature.keyImage' => ['required', 'regex:/\A0[23][a-f0-9]{64}\z/'],
        ]);

        $ballot = $box->submit($election, (int) $data['candidate'], $data['signature'], $crypto);
        return response()->json([
            'receipt' => $ballot->receipt,
            'bitcoin_txid' => $ballot->bitcoin_txid,
        ], $ballot->wasRecentlyCreated ? 201 : 200);
    }

    public function receipt(VoteList $election, string $receipt): JsonResponse
    {
        abort_unless($election->currentStage() === ElectionStage::Done && $election->ballot_definition, 404);
        $ballot = $election->ballots()->where('receipt', $receipt)->firstOrFail();
        $definition = $election->ballot_definition;

        return response()->json([
            'reference' => $definition['reference'],
            'ring' => $definition['ring'],
            'candidate' => $ballot->candidate_id,
            'signature' => $ballot->signature,
            'receipt' => $ballot->receipt,
            'bitcoin' => $definition['bitcoin'],
            'bitcoin_txid' => $ballot->bitcoin_txid,
            'bitcoin_confirmed' => $ballot->bitcoin_confirmed_at !== null,
        ]);
    }

    public function ledger(VoteList $election): JsonResponse
    {
        abort_unless($election->currentStage() === ElectionStage::Done && $election->ballot_definition, 404);

        return response()->json([
            'definition' => $election->ballot_definition,
            'ballots' => $election->ballots()->orderBy('receipt')->get([
                'candidate_id', 'signature', 'receipt', 'bitcoin_txid', 'bitcoin_confirmed_at',
            ]),
        ]);
    }
}
