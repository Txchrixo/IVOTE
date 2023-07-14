<?php

namespace App\Http\Requests\Election;

use App\Http\Requests\ApiRequest;
use App\Models\VoteList;

class ShowElectionRequest extends ApiRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('viewAny', VoteList::class);
    }

    public function rules(): array
    {
        return [
            'id' => ['required_without:election_id', 'integer', 'exists:vote,id'],
            'election_id' => ['required_without:id', 'integer', 'exists:vote,id'],
        ];
    }

    public function electionId(): int
    {
        return (int) ($this->validated('id') ?? $this->validated('election_id'));
    }
}
