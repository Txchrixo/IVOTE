<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Candidate\DeleteCandidateRequest;
use App\Http\Requests\Candidate\ListCandidatesRequest;
use App\Http\Requests\Candidate\UpdateCandidateRequest;
use App\Models\Candidate;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Storage;

class CandidateAPIController extends BaseAPIController
{
    /** Public list of the candidates of an election (no identity number). */
    public function getCandidates(ListCandidatesRequest $request): JsonResponse
    {
        $data = Candidate::where('vote_id', $request->validated('vote_id'))
            ->get(['id', 'name', 'des', 'photo', 'vote_id']);

        return $this->sendResponse($data, 'Candidates retrieved successfully!');
    }

    public function postUpdateCandidate(UpdateCandidateRequest $request): JsonResponse
    {
        $candidate = Candidate::findOrFail($request->validated('id'));
        $candidate->update($request->safe()->only(['name', 'des']));

        return $this->sendResponse($candidate->only(['id', 'name', 'des']), 'Candidate updated successfully!');
    }

    public function postDelCandidate(DeleteCandidateRequest $request): JsonResponse
    {
        $candidate = Candidate::findOrFail($request->validated('id'));

        if ($candidate->photo) {
            Storage::disk('public')->delete('candidates/' . basename($candidate->photo));
        }
        $candidate->delete();

        return $this->sendResponse(['id' => $candidate->id], 'Candidate deleted successfully!');
    }
}
