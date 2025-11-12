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
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'work_type' => 'required|in:full-time,part-time,freelance',
            'country_id' => 'required|exists:countries,id',
            'city_id' => 'required|exists:cities,id',
            'address' => 'nullable|string|max:255',
            'responsibilities' => 'required|string',
            'qualification' => 'required|string',
            'benefits' => 'required|string',
            'deadline' => 'nullable|date',
            'work_place' => 'required|in:hybrid,remote,on-site',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ];
    }
}
