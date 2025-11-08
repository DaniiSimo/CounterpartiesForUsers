<?php

namespace App\Http\Requests\User;

use App\DTO\RegistrationUserDTO;
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
            'name' => ['nullable', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', Password::defaults()],
        ];
    }

    public function dto(): RegistrationUserDTO
    {
        return RegistrationUserDTO::fromArray($this->validated());
    }
}
