<?php

namespace App\Http\Requests\Token;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rules\Password;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->wantsJson() || $this->ajax();
    }

    public function rules(): array
    {
        return [
            'email' => ['required', 'email', 'max:255'],
            'password' => ['required', 'string', Password::defaults()],
        ];
    }
}
