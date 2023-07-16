<?php

namespace App\Http\Controllers\API;

use App\Actions\AdvanceElectionStage;
use App\Exceptions\InvalidStageTransition;
use App\Http\Requests\Election\ChangeStageRequest;
use App\Http\Requests\Election\ShowElectionRequest;
use App\Http\Requests\Election\UpdateElectionRequest;
use App\Models\Key;
use App\Models\VoteList;
use App\Support\Audit;
use Illuminate\Http\JsonResponse;

/**
 * JSON endpoints of the election authority (routes protected by auth + can:ea).
 * Validation and authorization live in the form requests.
 */
class ElectionAPIController extends BaseAPIController
{
    /**
     * Moves the election to its next stage after re-authenticating the EA.
     */
    public function toggleStage(ChangeStageRequest $request, AdvanceElectionStage $advance): JsonResponse
    {
        $election = VoteList::findOrFail($request->validated('id'));

        try {
            $stage = $advance($election);
        } catch (InvalidStageTransition $e) {
            return $this->sendError('Invalid transition', $e->getMessage(), 422);
        }

        return $this->sendResponse(['stage' => $stage->value], 'Election stage updated successfully!');
    }

    public function getElectionDetails(ShowElectionRequest $request): JsonResponse
    {
        $election = VoteList::findOrFail($request->electionId());

        return $this->sendResponse($election, 'Election details retrieved successfully!');
    }

    public function updateElection(UpdateElectionRequest $request): JsonResponse
    {
        $election = VoteList::findOrFail($request->validated('id'));
        $election->update($request->electionData());
        Audit::record('election.updated', $election, ['fields' => array_keys($election->getChanges())]);

        return $this->sendResponse($election->fresh(), 'Election updated successfully!');
    }

    /**
     * Voter addresses of an election (never the private keys).
     */
    public function getAllBitcoinAddress(ShowElectionRequest $request): JsonResponse
    {
        $data = Key::where('used_for', $request->electionId())->get(['id', 'bitcoin_address', 'is_paid']);

        return $this->sendResponse($data, 'Addresses retrieved successfully!');
    }
}
