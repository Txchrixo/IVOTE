<?php

namespace App\Http\Requests\Candidate;

use App\Http\Requests\ApiRequest;

/**
 * Public listing of the candidates of an election.
 */
class ListCandidatesRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'vote_id' => ['bail', 'required', 'integer', 'exists:vote,id'],
        ];
    }
}
