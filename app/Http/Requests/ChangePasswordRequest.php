<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class ChangePasswordRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->user() !== null;
    }

    public function rules(): array
    {
        return [
            'password_old' => ['required', 'string', 'current_password'],
            'password' => ['required', 'string', 'confirmed', 'max:255', 'different:password_old', Password::defaults()],
        ];
    }
}
