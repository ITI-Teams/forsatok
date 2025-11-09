<?php

namespace App\Domains\Location\Requests\Country;

use Illuminate\Foundation\Http\FormRequest;

class UpdateCountryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255|unique:countries,name,' . $this->country_id,
            'code' => 'nullable|string|max:3|unique:countries,code,' . $this->country_id,
        ];
    }
}

