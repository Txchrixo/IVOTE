<?php

namespace App\Http\Controllers\RA;

use App\Support\Audit;
use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\VoteList;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Requests\Candidate\StoreCandidateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Str;
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
    public function postAddCandidate(StoreCandidateRequest $request): RedirectResponse
    {
        $data = $request->safe()->only(['name', 'cni', 'des', 'vote_id']);

        if ($request->hasFile('photo')) {
            $photo = $request->file('photo');
            $data['photo'] = Str::random(32) . '.' . $photo->extension();
            $photo->storeAs('candidates', $data['photo'], 'public');
        }

        $candidate = Candidate::create($data);
        Audit::record('candidate.created', $candidate);

        return to_route('ra.addCandidate')->with('success', 'Candidate registered successfully.');
    }
}
