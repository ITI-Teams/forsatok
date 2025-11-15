<?php

namespace App\Domains\Contact\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreContactMessageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'full_name' => ['required', 'string', 'max:255'],
            'email'     => ['required', 'email', 'max:255'],
            'subject'   => ['nullable', 'string', 'max:255'],
            'message'   => ['required', 'string'],
            'contactable_type' => 'nullable|string',
            'contactable_id' => 'nullable|integer',
            'user_id' => 'nullable|integer|exists:users,id',
        ];
    }
}
