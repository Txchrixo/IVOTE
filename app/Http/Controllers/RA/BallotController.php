<?php

namespace App\Http\Controllers\RA;

use App\Enums\ElectionStage;
use App\Http\Controllers\Controller;
use App\Models\Code;
use App\Models\User;
use App\Models\VoteList;
use App\Support\Audit;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class BallotController extends Controller
{
    public function getDashBoard(): View
    {
        $data = Auth::user();
        $data['ea_address'] = User::where('role', 2)->value('bitcoin_address');
        $data['voters'] = Code::where('public_key', '<>', '0')->count();

        return view('ra.dashboard', ['data' => $data]);
    }

    public function getAddVoter(): View
    {
        return view('ra.addVoter', ['elections' => $this->openElections()]);
    }

    public function getBallot(): View
    {
        return view('ra.ballot', ['data' => Code::latest('id')->paginate(50), 'elections' => $this->openElections()]);
    }

    public function postVoter(Request $request): RedirectResponse
    {
        $request->merge(['number' => 1]);
        return $this->postBallot($request);
    }

    public function postBallot(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'number' => ['required', 'integer', 'min:1', 'max:128'],
            'election_id' => ['required', 'integer', 'exists:vote,id'],
        ]);

        DB::transaction(function () use ($data) {
            $election = VoteList::whereKey($data['election_id'])->lockForUpdate()->firstOrFail();
            $issued = Code::where('item_id', $election->id)->count();

            if (!in_array($election->currentStage(), [ElectionStage::Initial, ElectionStage::Veriff], true)) {
                throw ValidationException::withMessages(['election_id' => 'Code issuance is closed.']);
            }
            if ($issued + $data['number'] > min((int) $election->number, 128)) {
                throw ValidationException::withMessages(['number' => 'This exceeds the available codes for this election.']);
            }

            for ($index = 0; $index < $data['number']; $index++) {
                Code::create(['code' => strtoupper(bin2hex(random_bytes(10))), 'item_id' => $election->id]);
            }
            Audit::record('election.codes_issued', $election, ['count' => (int) $data['number']]);
        });

        return to_route('ra.ballot')->with('success', 'Ballot codes created.');
    }

    private function openElections(): Collection
    {
        return VoteList::whereIn('stage', [ElectionStage::Initial->value, ElectionStage::Veriff->value])->get();
    }
}
