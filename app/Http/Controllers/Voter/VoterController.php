<?php

namespace App\Http\Controllers\Voter;

use App\Enums\ElectionStage;
use App\Enums\Role;
use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\VoteList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class VoterController extends Controller
{
    /**
     * Veriff Page
     * @param Request $request
     * @return View | \Illuminate\Http\RedirectResponse
     */
    public function getVeriff(Request $request, string $election): View|RedirectResponse|\Illuminate\Http\Response
    {
        $slug = VoteList::slug($election);
        $open = VoteList::where('stage', ElectionStage::Veriff->value);

        $exact = (clone $open)->where('title', $slug)->first();
        if ($exact) {
            return view('voter.veriff', ['election' => $exact->title]);
        }

        // Close match among elections in the verification stage: redirect to the canonical URL
        $like = '%' . addcslashes($slug, '%_\\') . '%';
        $close = (clone $open)->where('title', 'like', $like)->orderBy('id')->first();
        if ($close) {
            return to_route('voter.veriff', ['election' => VoteList::slug($close->title)]);
        }

        return response()->view('voter.veriff-fail', ['election' => $election], 404);
    }

    public function getVeriffSubmitted(Request $request)
    {
        return view('voter.veriff-submitted');
    }

    public function getReady(Request $request)
    {
        $election = VoteList::where('title', VoteList::slug((string) $request->query('election', '')))->first()
            ?? VoteList::where('stage', ElectionStage::Veriff->value)->orderBy('id')->first();

        return view('voter.ready', ['election' => $election?->title ?? '']);
    }

    public function getGenerateKeys(Request $request)
    {
        return view('voter.generate-keys');
    }

    public function getWait(Request $request)
    {
        return view('voter.wait');
    }

    public function getVote(Request $request)
    {
        return view('voter.vote');
    }

    public function getTallyHome(Request $request)
    {
        $vote_list = VoteList::where('stage', ElectionStage::Done->value)->get();
        return  view('tally.home', ['data' => $vote_list]);
    }

    /** Results of an election (the tally script reads the EA bitcoin address). */
    public function getTally(Request $request): View|RedirectResponse
    {
        return $this->electionPage($request, 'tally.tally', 'tally.home');
    }

    /** Vote verification for an election. */
    public function getVerify(Request $request): View|RedirectResponse
    {
        return $this->electionPage($request, 'verify.verify', 'verify.home');
    }

    public function getVerifyHome(): View
    {
        return view('verify.home', ['data' => VoteList::where('stage', ElectionStage::Done->value)->get()]);
    }

    private function electionPage(Request $request, string $view, string $fallbackRoute): View|RedirectResponse
    {
        $election = VoteList::find((int) $request->query('vote_item'));
        if (!$election) {
            return to_route($fallbackRoute);
        }

        $authority = User::where('role', Role::EA->value)->first();

        return view($view, [
            'item_id' => $election->id,
            'title' => $election->title,
            'eaaddress' => $authority?->bitcoin_address,
        ]);
    }

    //    public function getVoteList($request, $response)
    //    {
    //        $data = UserVote::all()->where('user_id',$this->auth->user()->id)->first();
    //        $result = VoteList::all()->where('id',$data['id']);
    //
    //        return $this->view->render($response,'voter/vote.twig',['data'=>$result]);
    //    }
    //
    //    public function postVote($request,$response)
    //    {
    //
    //        $validation = $this->validator->validate($request,[
    //            'code' => v::noWhitespace()->notEmpty()->codeAvailable()
    //        ]);
    //
    //        if ($validation->failed()) {
    //            return $response->withRedirect($this->router->pathFor('voter.vote'));
    //        }
    //        $code = Code::useCode($request->getParam('code'));
    //
    //        UserVote::create([
    //            'user_id' => $this->auth->user()->id,
    //            'vote_id' => $code->item_id
    //        ]);
    //
    //        return $response->withRedirect($this->router->pathFor('voter.vote'));
    //    }

}
