<?php

namespace App\Http\Requests\Election;

use App\Models\VoteList;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\File;

class UploadBackgroundRequest extends FormRequest
{
    public function authorize(): bool
    {
        $election = VoteList::find($this->input('election'));

        return $election === null || $this->user()->can('update', $election);
    }

    public function rules(): array
    {
        return [
            'election' => ['required', 'integer', 'exists:vote,id'],
            // Real MIME type check (content sniffing), no SVG, size and dimension limits
            'file' => ['required', File::image()->types(['jpeg', 'png', 'webp'])->max(2048)->dimensions(
                \Illuminate\Validation\Rule::dimensions()->maxWidth(4000)->maxHeight(4000)
            )],
        ];
    }
}
