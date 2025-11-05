<?php

namespace App\Domains\Employers\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateEmployerInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'company_name' => ['required', 'string', 'max:255', 'regex:/^[\p{L}\p{N}\s\-\.&]+$/u'],
            'industry' => ['nullable', 'string', 'max:100'],
            'location' => ['nullable', 'string', 'max:120'],
            'about' => ['nullable', 'string', 'max:1000'],
            'website' => ['nullable', 'url', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'company_name.required' => 'Company name is required.',
            'company_name.max' => 'Company name must be at most 255 characters.',
            'company_name.regex' => 'Company name may include letters, numbers, spaces, and - . & only.',

            'industry.string' => 'Industry must be a text value.',
            'industry.max' => 'Industry must be at most 100 characters.',

            'location.string' => 'Location must be a text value.',
            'location.max' => 'Location must be at most 120 characters.',

            'about.string' => 'About must be a text value.',
            'about.max' => 'About must be at most 1000 characters.',

            'website.url' => 'Please enter a valid URL (e.g., https://example.com).',
            'website.max' => 'Website must be at most 255 characters.',
        ];
    }
}


