<?php

namespace App\Http\Requests\Election;

use App\Models\VoteList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreElectionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', VoteList::class);
    }

    protected function prepareForValidation(): void
    {
        // Titles are stored as slugs (see VoteList::title()), uniqueness is checked on the slug
        $this->merge(['slug' => VoteList::slug((string) $this->input('title'))]);
    }

    public function rules(): array
    {
        return [
            'title' => ['required', 'string', 'min:2', 'max:255'],
            'slug' => [Rule::unique('vote', 'title')],
            'number' => ['required', 'integer', 'min:1', 'max:5000000'],
            'description' => ['nullable', 'string', 'max:2000'],
        ];
    }

    public function messages(): array
    {
        return ['slug.unique' => 'An election with this title already exists.'];
    }
}
