<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\bgElectionUpload;
use App\Http\Requests\PostElectionRequest;
use App\Models\Code;
use App\Models\Key;
use App\Models\User;
use App\Models\VoteList;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ElectionAPIController extends BaseAPIController
{

    /**
     * API: toggle the voting
     * Goal: If the item of the voting is started,
     *       then stop it by marking the is_started as 0,
     *       Else start the item of the voting by marking the is_started as 1.
     *
     * ($vote + 1) % 2 is a great mathematics way to achieve this goal which is smarter and more efficiency
     * compared to the way of using if sentences.
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function toggleStage(Request $request, Response $response)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'bail|required|integer|exists:Vote,id',
            'password' => 'required|max:255',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $EA = User::where('username', 'ea')->first();
        $Authorized = Hash::check($validator->safe(['password'])['password'], $EA->password);

        if (!$Authorized) {
            return $this->sendError('Unauthorized', 'Password Incorrect', 401);
        }

        $election = VoteList::find($validator->safe()->only(['id']))->first();
        $election->stage += 1;
        $election->save();


        return $this->sendResponse(['stage' => $election->stage], 'Election stage updated successfully!');
    }

    /**
     * API: get all keys for vote id
     *
     * @param \Illuminate\Http\Request $request
     * @param \Illuminate\Http\Response $response
     * @return \Illuminate\Http\JsonResponse
     */
    public function getKeysList(Request $request, Response $response)
    {
        $id = (int) $request->input('id');
        $data = Key::where('used_for', $id)->get();
        return response()->json($data);
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function getElectionDetails(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'bail|required|integer|exists:Vote,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $data = VoteList::where('id', $validator->safe()->only(['id']))->first();

        return $this->sendResponse($data, 'Election details retrieved successfully!');
    }

    /**
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function updateElection(Request $request): \Illuminate\Http\JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'id' => 'bail|required|integer|exists:Vote,id',
            'title' => 'bail|required|min:2|max:255',
            'number' => 'bail|required|integer|min:3|max:5000000',
            'start_vote' => 'bail|nullable|date|required_with:end_vote|after:tomorrow',
            'end_vote' => 'bail|nullable|date|required_with:start_vote|after:start_date',
            'start_veriff' => 'bail|nullable|date|required_with:end_veriff|after:tomorrow',
            'end_veriff' => 'bail|nullable|date|required_with:start_veriff|after:start_date',
            'stage' => 'bail|integer|exists:Vote,stage',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $data = VoteList::where('id', $validator->safe()->only(['id']))->update($validator->safe()->except(['id']));

        return $this->sendResponse($data, 'Election updated successfully!');
    }

    /**
     * Get all bitcoin address
     * @param $request
     * @return mixed
     */
    public function getAllBitcoinAddress(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'election_id' => 'bail|required|integer|exists:Vote,id',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $data = Key::where('used_for', $validator->safe()->only(['election_id']))->get(['id', 'bitcoin_address', 'is_paid']);

        if ($data) {
            return $this->sendResponse($data, 'bitcoin found successfully!');
        } else {
            return $this->sendError($data, 'Not found any bitcoin Address', 401);
        }
    }
}
