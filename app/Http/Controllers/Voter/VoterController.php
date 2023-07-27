<?php

namespace App\Http\Controllers\Voter;

use App\Enums\ElectionStage;
use App\Http\Controllers\Controller;
use App\Models\VoteList;
use App\Services\BallotBox;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\View\View;

class VoterController extends Controller
{
    public function getVeriff(string $election): View|RedirectResponse|Response
    {
        $slug = VoteList::slug($election);
        $open = VoteList::where('stage', ElectionStage::Veriff->value);
        $exact = (clone $open)->where('title', $slug)->first();
        if ($exact) {
            return view('voter.veriff', ['election' => $exact]);
        }

        $like = '%' . addcslashes($slug, '%_\\') . '%';
        $close = $open->where('title', 'like', $like)->orderBy('id')->first();
        if ($close) {
            return to_route('voter.veriff', ['election' => VoteList::slug($close->title)]);
        }

        return response()->view('voter.veriff-fail', ['election' => $election], 404);
    }

    public function getVeriffSubmitted(): View
    {
        return view('voter.veriff-submitted');
    }

    public function getReady(Request $request): View
    {
        $election = VoteList::where('title', VoteList::slug((string) $request->query('election', '')))->first()
            ?? VoteList::where('stage', ElectionStage::Veriff->value)->orderBy('id')->first();

        return view('voter.ready', ['election' => $election?->title ?? '', 'electionId' => $election?->id]);
    }

    public function getGenerateKeys(Request $request): View
    {
        $open = VoteList::where('stage', ElectionStage::Veriff->value)->orderBy('id');
        $election = $request->filled('election') ? (clone $open)->findOrFail((int) $request->query('election')) : null;

        return view('voter.generate-keys', ['election' => $election, 'elections' => $election ? [] : $open->get()]);
    }

    public function getWait(Request $request): View
    {
        return view('voter.wait', ['election' => VoteList::find((int) $request->query('election'))]);
    }

    public function getVote(Request $request, BallotBox $box): View
    {
        $open = VoteList::where('stage', ElectionStage::Ongoing->value)->orderBy('id');
        $election = $request->filled('election') ? (clone $open)->findOrFail((int) $request->query('election')) : null;

        return view('voter.vote', [
            'election' => $election,
            'elections' => $election ? [] : $open->get(),
            'definition' => $election ? $box->definition($election) : null,
        ]);
    }

    public function getFailed(): View
    {
        return view('voter.fail');
    }

    public function getTallyHome(): View
    {
        return $this->closedElections('tally.home');
    }

    public function getVerifyHome(): View
    {
        return $this->closedElections('verify.home');
    }

    private function closedElections(string $view): View
    {
        return view($view, ['data' => VoteList::where('stage', ElectionStage::Done->value)->get()]);
    }

    public function getTally(Request $request): View|RedirectResponse
    {
        $election = $this->closedElection($request);
        if (!$election) {
            return to_route('tally.home');
        }

        $definition = $election->ballot_definition;
        $counted = $election->ballots();
        if ($definition['bitcoin'] ?? false) {
            $counted->whereNotNull('bitcoin_confirmed_at');
        }
        $counts = $counted->selectRaw('candidate_id, COUNT(*) AS total')->groupBy('candidate_id')->pluck('total', 'candidate_id');

        return view('tally.tally', [
            'election' => $election,
            'candidates' => $definition['candidates'] ?? [],
            'counts' => $counts,
            'pending' => $election->ballots()->count() - $counts->sum(),
        ]);
    }

    public function getVerify(Request $request): View|RedirectResponse
    {
        $election = $this->closedElection($request);

        return $election ? view('verify.verify', ['election' => $election]) : to_route('verify.home');
    }

    private function closedElection(Request $request): ?VoteList
    {
        return VoteList::where('stage', ElectionStage::Done->value)->find((int) $request->query('vote_item'));
    }
}
