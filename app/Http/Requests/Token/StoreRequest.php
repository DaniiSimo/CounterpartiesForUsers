<?php

namespace App\Http\Requests\Token;

use App\DTO\ApiAuthenticationUserDTO;
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

    public function dto(): ApiAuthenticationUserDTO
    {
        return ApiAuthenticationUserDTO::fromArray(array_merge($this->validated(), ['ip' => $this->ip()]));
    }
}
