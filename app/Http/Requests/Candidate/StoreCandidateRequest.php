<?php

namespace App\Http\Requests\Candidate;

use App\Models\Candidate;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class StoreCandidateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user()->can('create', Candidate::class);
    }

    public function rules(): array
    {
        return [
            'name' => ['bail', 'required', 'string', 'max:135'],
            'cni' => ['bail', 'required', 'string', 'min:8', 'max:32', 'alpha_num'],
            'des' => ['bail', 'required', 'string', 'max:500'],
            'vote_id' => ['bail', 'required', 'integer', 'exists:vote,id'],
            'photo' => ['nullable', File::image()->types(['jpeg', 'png', 'webp'])->max(2048)],
        ];
    }
}
