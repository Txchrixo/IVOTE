<?php

namespace App\Http\Controllers\EA;

use App\Http\Controllers\Controller;
use App\Http\Requests\bgElectionUpload;
use App\Http\Requests\PostElectionRequest;
use App\Models\Code;
use App\Models\VoteList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class VoteController extends Controller
{
    /**
     * Dashboard Page
     * @param Request $request
     * @return View
     */
    public function getDashBoard(Request $request): Response
    {
        $data = Auth::user();
        $data["items"] = VoteList::all()->count();

        $data["voters"] = Code::select('public_key')->where([
            ['public_key', '<>', '0']
        ])->count();

        $voteList = VoteList::all()->take(5);

        return response()
            ->view('ea.dashboard', ['data' => $data, 'election' => $voteList]);
        //->header('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
    }

    /**
     * VoteList page
     * @param Request $request
     * @return View
     */
    public function getElectionList(Request $request)
    {
        $data = VoteList::all();

        return view('ea.election', ['data' => $data]);
    }

    /**
     * Add a Vote Page
     * @param Request $request
     * @return View
     */
    public function getAddElection(Request $request)
    {
        $elections = VoteList::all(['id', 'title', 'bg']);
        return view('ea.addElection', ['elections' => $elections]);
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function postElection(PostElectionRequest $request)
    {
        $validator = $request->safe();

        $vote_item = VoteList::create([
            'title' => $request->input('title'),
            'number' => $request->input('number'),
            'description' => $request->input('description') ?? null,
        ]);

        return to_route('ea.election');
    }
    public function uploadBg(bgElectionUpload $request)
    {
        $electionId = $request->input('election');
        $election = VoteList::find($electionId);

        $bg_path = 'uploads/bg';
        $uploadedFile = $request->file('file');
        $fileName = 'bg-' . $electionId . '.jpg';

        if(Storage::disk('public')->exists($bg_path.'/'.$fileName)){
            Storage::delete($fileName);
        }

        $filePath = $uploadedFile->storeAs($bg_path, $fileName, 'public');

        //Save the image path to the election
        $election->bg = $fileName;
        $election->save();

        return back()->with('success', 'Background uploaded successfully.')->withInput([
            'election' => $electionId
        ]);
    }
}
