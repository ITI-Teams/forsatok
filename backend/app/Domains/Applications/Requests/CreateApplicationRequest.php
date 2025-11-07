<?php


namespace App\Domains\Applications\Requests;


use Illuminate\Foundation\Http\FormRequest;


class CreateApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {

        return true;
    }


    public function rules(): array
    {
        return self::creationRules();
    }


    public static function creationRules(): array
    {
        return [
            // 'name' => ['required', 'string', 'max:255'],
            // 'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            // 'password' => ['required', 'string', 'min:8', 'confirmed'],
            // 'type' => ['required', 'in:admin,employer,candidate'],
        ];
    }


    public function validatedPayload(): array
    {
        return $this->validated();
    }
}

