<?php

namespace App\Http\Controllers\RA;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\VoteList;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class CandidateController extends Controller
{
    /**
     * Add candidate
     * @param Request $request
     * @return View
     */
    public function getAddCandidate(Request $request): View
    {
        $data = VoteList::all();

        return view('ra.addCandidate', ['data' => $data]);
    }

    /**
     * Get Candidate List
     * @param Request $request
     * @return View
     */
    public function getCandidatesList(Request $request): View
    { 
        $data = VoteList::all();

        return view('ra.candidates', ['data' => $data]);
    }
    /**
     * Post Candidate 
     * @param Request $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function postAddCandidate(Request $request) : \Illuminate\Http\RedirectResponse
    { 
        $validator = Validator::make($request->all(), [
            'name' => 'bail|required|max:135',
            'cni' => 'bail|required|min:8|max:255',
            'des' => 'bail|required|max:500',
            'vote_id' => 'bail|required|integer|exists:Vote,id',
            'file' => 'bail|nullable|image|mimes:jpeg,png,jpg,gif|max:2048', 
        ]);

        $data = $validator->safe()->all();

        $vote_item = Candidate::create($data);

        return to_route('ra.addCandidate')->with('success', 'Candidate registered successfully.');
    }


}
