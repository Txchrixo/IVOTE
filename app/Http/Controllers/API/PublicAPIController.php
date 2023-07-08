<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseAPIController;
use App\Models\Code;
use App\Models\Veriff;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;
use BlockIo\Client;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class PublicAPIController extends BaseAPIController
{
    public function getAllBitcoinAddress(Request $request)
    {
        $query = new Code();
        $data = $query->select('public_key')->where([
            ['public_key', '<>', '0']
        ])->get();

        return response()->json($data);
    }

    public function getBalance(Request $request)
    {
        $apiKey = "REDACTED_BLOCKIO_KEY";
        $version = 2; // API version
        $pin = "REDACTED_BLOCKIO_PIN";
        $block_io = new Client($apiKey, $pin, $version);

        $response = $block_io->get_balance();

        return $response;
    }
    public function getAddressBalance(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'address' => 'bail|required',
        ]);

        if ($validator->fails()) {
            return $this->sendError('Validation Error', $validator->errors(), 422);
        }

        $apiKey = "REDACTED_BLOCKIO_KEY";
        $version = 2; // API version
        $pin = "REDACTED_BLOCKIO_PIN";
        $block_io = new Client($apiKey, $pin, $version);

        $result = $block_io->get_address_balance(array('addresses' => $validator->safe(['address'])['address']));

        return $this->sendResponse($result, 'Address balance retrieved!');
    }

    public function getAllPubKeys(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'election_id' => 'bail|required|integer|exists:Vote,id',
            'code' => 'nullable|exists:Code,code'
        ]);

        $id = $validator->safe()->only(['election_id'])['election_id'];


        $data = DB::table('code')
        ->select('public_key')
        ->where('item_id', $id)
        ->where('public_key', '<>', '0');

        if($code = $validator->safe()->only(['code'])){
            $data->where('code', '<>', $code);
        }

        $data = $data->get();

        return $this->sendResponse($data, 'Address balance retrieved!');
    }
    public function postVeriffParams(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'sessionId' => 'bail|required',
            'hmacSignature' => 'bail|required'
        ]);

        $data = $validator->safe()->all();


        //Veriff::create($data);


        return $this->sendResponse($data, 'veriff stocked!');
    }
}
