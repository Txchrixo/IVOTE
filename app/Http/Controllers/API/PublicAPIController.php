<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\API\BaseAPIController;
use App\Models\Code;
use Illuminate\Http\Request;
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
}
