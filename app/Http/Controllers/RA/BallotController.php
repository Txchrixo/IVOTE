<?php

namespace App\Http\Controllers\RA;

use App\Http\Controllers\Controller;
use App\Models\Candidate;
use App\Models\Code;
use App\Models\User;
use App\Models\VoteList;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;

class BallotController extends Controller
{
    /**
     * Dashboard Page
     * @param Request $request
     * @return View
     */
    public function getDashBoard(Request $request): View
    {
        $data = Auth::user();

        $data['ea_address'] = User::where('role', 2)->first('bitcoin_address')->bitcoin_address;

        $data["voters"] = Code::where([
            ['public_key', '<>', '0']
        ])->get('public_key')->count();

        return view('ra.dashboard', ['data' => $data]);
    }

    /**
     * AddVoter page
     * @param Request $request
     * @return View
     */
    public function getAddVoter(Request $request): View
    {
        $data = VoteList::all();

        return view('ra.addVoter', ['data' => $data]);
    }

    /**
     * Ballot page
     * @param Request $request
     * @return View
     */
    public function getBallot(Request $request): View
    {
        $data = Code::all();

        return view('ra.ballot', ['data' => $data]);
    }



}
