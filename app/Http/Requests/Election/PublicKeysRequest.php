<?php

namespace App\Http\Requests\Election;

use App\Http\Requests\ApiRequest;

/**
 * Public list of the voters' public keys of an election.
 * The optional code is not checked for existence, so the endpoint cannot be used
 * to find out which voting codes are valid.
 */
class PublicKeysRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'election_id' => ['bail', 'required', 'integer', 'exists:vote,id'],
            'code' => ['nullable', 'string', 'max:64'],
        ];
    }
}
