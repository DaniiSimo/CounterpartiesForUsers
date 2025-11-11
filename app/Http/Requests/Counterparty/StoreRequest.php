<?php

namespace App\Http\Requests\Counterparty;

use Illuminate\Foundation\Http\FormRequest;

class StoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return $this->wantsJson() || $this->ajax();
    }

    public function rules(): array
    {
        return [
            'inn' => ['required', 'string', 'regex:/^(?:\d{10}|\d{12})$/'],
        ];
    }
}
