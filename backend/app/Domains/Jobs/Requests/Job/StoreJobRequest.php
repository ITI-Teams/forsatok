<?php

namespace App\Domains\Jobs\Requests\Job;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class StoreJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return Auth::check();
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'experience' => 'required|string|max:255',
            'description' => 'required|string',
            'salary_min' => 'nullable|numeric|max:99999999.99',
            'salary_max' => 'nullable|numeric|max:99999999.99',
            'type' => 'required|in:full-time,part-time,remote,internship',
            'location' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ];
    }
}
