<?php

namespace App\Http\Controllers\API;

use App\Models\Candidate;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CandidateAPIController extends BaseAPIController
{
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getCandidates(Request $request) : \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'vote_id' => 'bail|required|integer|exists:Vote,id',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $data = Candidate::where('vote_id', $validator->safe()->only(['vote_id']))->get();

        return $this->sendResponse($data, 'Election details retrieved successfully!');
    }
    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function postDelCandidate(Request $request) : \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'bail|required|integer|exists:Candidate,id',
        ]);

        if($validator->fails()){
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $data = Candidate::where('id', $validator->safe()->only(['id']))->delete();

        return $this->sendResponse($data, 'Candidate deleted successfully!');
    }

}
