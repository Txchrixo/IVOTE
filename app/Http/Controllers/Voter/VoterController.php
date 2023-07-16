<?php

namespace App\Http\Controllers\Voter;

use App\Enums\ElectionStage;
use App\Http\Controllers\Controller;
use App\Models\VoteList;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Response;
use SebastianBergmann\Type\ObjectType;

class VoterController extends Controller
{
    /**
     * Veriff Page
     * @param Request $request
     * @return View | \Illuminate\Http\RedirectResponse
     */
    public function getVeriff(Request $request, string $election)
    {
        $election = str_replace(' ', '-', strtolower($election));
        $data = VoteList::where('title', $election)
            ->where('stage', ElectionStage::Veriff->value);

        if ($data->get()->isNotEmpty()) {
            $exactMatch = $data->get()->first();
            return view('voter.veriff')->with('election', $exactMatch->title);
        }

        $data = $data->orWhere('title', 'like', '%' . $election . '%')->get();

        if ($data->isEmpty()) {
            abort(404);
        }

        $matchingTitle = $data->first()->title;
        return to_route('voter.veriff')->with('election', $matchingTitle);
    }

    public function getVeriffSubmitted(Request $request)
    {
        return view('voter.veriff-submitted');
    }

    public function getReady(Request $request)
    {
        return view('voter.ready', ['election' => 'Presidentielle 2023']);
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
        $vote_list = VoteList::where('stage', 4)->get();
        return  view('tally.home', ['data' => $vote_list]);
    }

    public function getTally($request, $response)
    {
        $vote_item = (int) $request->getParam('vote_item');
        $eaddress = User::select('bitcoin_address')->where('id', '2')->first();
        if (!$vote_item)
            return $response->withRedirect($this->router->pathFor('tally.home'));
        $result = VoteList::all()->where('id', $vote_item)->first();
        if (!$result)
            return $response->withRedirect($this->router->pathFor('tally.home'));

        return $this->view->render($response, 'tally/tally.twig', ['item_id' => $vote_item, 'title' => $result->title, 'eaaddress' => $eaddress->bitcoin_address]);
    }


    public function getVerify($request, $response)
    {
        $vote_item = (int) $request->getParam('vote_item');
        $eaddress = User::select('bitcoin_address')->where('id', '2')->first();
        if (!$vote_item)
            return $response->withRedirect($this->router->pathFor('verify.home'));
        $result = VoteList::all()->where('id', $vote_item)->first();
        if (!$result)
            return $response->withRedirect($this->router->pathFor('verify.home'));

        return $this->view->render($response, 'verify/verify.twig', ['item_id' => $vote_item, 'title' => $result->title, 'eaaddress' => $eaddress->bitcoin_address]);
    }

    public function getVerifyHome($request, $response)
    {
        $vote_list = VoteList::all();
        return $this->view->render($response, 'verify/home.twig', ['data' => $vote_list]);
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
