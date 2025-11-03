<?php


namespace App\Domains\Users\Requests;


use Illuminate\Foundation\Http\FormRequest;


class CreateUserRequest extends FormRequest
{
    public function authorize(): bool
    {
        // adapt this according to your auth rules
        return true;
    }


    public function rules(): array
    {
        return self::creationRules();
    }


    public static function creationRules(): array
    {
        return [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ];
    }


    public function validatedPayload(): array
    {
        return $this->validated();
    }
}

