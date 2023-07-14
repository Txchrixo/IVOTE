<?php

namespace App\Http\Controllers\EA;

use App\Http\Controllers\Controller;
use App\Http\Requests\Election\StoreElectionRequest;
use App\Http\Requests\Election\UploadBackgroundRequest;
use App\Models\Code;
use App\Models\VoteList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
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
    public function postElection(StoreElectionRequest $request): RedirectResponse
    {
        VoteList::create($request->safe()->only(['title', 'number', 'description']));

        return to_route('ea.election')->with('success', 'Election added successfully!');
    }

    /**
     * Stores the election background under a generated name with the extension of the
     * detected MIME type, and removes the previous file.
     */
    public function uploadBg(UploadBackgroundRequest $request): RedirectResponse
    {
        $election = VoteList::findOrFail($request->validated('election'));
        $disk = Storage::disk('public');

        if ($election->bg && $disk->exists('uploads/bg/' . basename($election->bg))) {
            $disk->delete('uploads/bg/' . basename($election->bg));
        }

        $file = $request->file('file');
        $fileName = 'bg-' . $election->id . '-' . Str::random(16) . '.' . $file->extension();
        $file->storeAs('uploads/bg', $fileName, 'public');

        $election->bg = $fileName;
        $election->save();

        return back()->with('success', 'Background uploaded successfully.')->withInput([
            'election' => $election->id,
        ]);
    }
}
