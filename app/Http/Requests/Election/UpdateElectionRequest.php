<?php

namespace App\Http\Requests\Election;

use App\Http\Requests\ApiRequest;
use App\Models\VoteList;
use Illuminate\Validation\Rule;

class UpdateElectionRequest extends ApiRequest
{
    public function authorize(): bool
    {
        $election = VoteList::find($this->input('id'));

        return $election === null || $this->user()->can('update', $election);
    }

    protected function prepareForValidation(): void
    {
        $this->merge(['slug' => VoteList::slug((string) $this->input('title'))]);
    }

    public function rules(): array
    {
        return [
            'id' => ['bail', 'required', 'integer', 'exists:vote,id'],
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'slug' => [Rule::unique('vote', 'title')->ignore($this->input('id'))],
            'number' => ['required', 'integer', 'min:1', 'max:5000000'],
            'description' => ['nullable', 'string', 'max:2000'],
            // Verification first, then the vote, each window in the future and well ordered
            'start_veriff' => ['nullable', 'date', 'after:now', 'required_with:end_veriff'],
            'end_veriff' => ['nullable', 'date', 'after:start_veriff', 'required_with:start_veriff'],
            'start_vote' => ['nullable', 'date', 'after:now', 'after_or_equal:end_veriff', 'required_with:end_vote'],
            'end_vote' => ['nullable', 'date', 'after:start_vote', 'required_with:start_vote'],
        ];
    }

    public function messages(): array
    {
        return ['slug.unique' => 'An election with this title already exists.'];
    }

    /** Only these attributes may be written. */
    public function electionData(): array
    {
        return $this->safe()->only(['title', 'number', 'description', 'start_veriff', 'end_veriff', 'start_vote', 'end_vote']);
    }
}
