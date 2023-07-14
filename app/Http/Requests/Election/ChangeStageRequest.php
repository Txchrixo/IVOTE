<?php

namespace App\Http\Requests\Election;

use App\Http\Requests\ApiRequest;
use App\Models\VoteList;

class ChangeStageRequest extends ApiRequest
{
    public function authorize(): bool
    {
        $election = VoteList::find($this->input('id'));

        return $election === null || $this->user()->can('changeStage', $election);
    }

    public function rules(): array
    {
        return [
            'id' => ['bail', 'required', 'integer', 'exists:vote,id'],
            // Re-authentication of the logged-in EA
            'password' => ['required', 'string', 'max:255', 'current_password'],
        ];
    }
}
