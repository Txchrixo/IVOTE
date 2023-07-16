<?php

namespace App\Http\Controllers\API;

use App\Http\Requests\Election\PublicKeysRequest;
use App\Models\Code;
use Illuminate\Http\JsonResponse;

class PublicAPIController extends BaseAPIController
{
    /**
     * Public keys registered for an election, optionally excluding the caller's own code.
     */
    public function getAllPubKeys(PublicKeysRequest $request): JsonResponse
    {
        $query = Code::query()
            ->where('item_id', $request->validated('election_id'))
            ->whereNotNull('public_key')
            ->where('public_key', '<>', '0');

        if ($code = $request->validated('code')) {
            $query->where('code', '<>', $code);
        }

        return $this->sendResponse($query->pluck('public_key')->map(fn ($key) => ['public_key' => $key]), 'Public keys retrieved!');
    }
}
