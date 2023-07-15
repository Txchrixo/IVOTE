<?php

namespace App\Http\Requests\Candidate;

use App\Http\Requests\ApiRequest;
use App\Models\Candidate;

class UpdateCandidateRequest extends ApiRequest
{
    public function authorize(): bool
    {
        $candidate = Candidate::find($this->input('id'));

        return $candidate === null || $this->user()->can('update', $candidate);
    }

    public function rules(): array
    {
        return [
            'id' => ['bail', 'required', 'integer', 'exists:candidate,id'],
            'name' => ['bail', 'required', 'string', 'max:135'],
            'des' => ['bail', 'required', 'string', 'max:500'],
        ];
    }
}
