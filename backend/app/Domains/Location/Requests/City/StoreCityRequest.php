<?php

namespace App\Domains\Location\Requests\City;

use Illuminate\Foundation\Http\FormRequest;

class StoreCityRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:cities,name',
            'country_id' => 'required|exists:countries,id',
        ];
    }
}

