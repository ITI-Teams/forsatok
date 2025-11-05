<?php

namespace App\Http\Requests\Jobs;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
         // only the creator of the job can update the job
        $job = $this->route('job'); // assuming route-model binding

        return Auth::check()&& Auth::id() === $job->employer_id;;
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
            'experince' => 'required|string|max:255',
            'description' => 'required|string',
            'salary_min' => 'nullable|numeric',
            'salary_max' => 'nullable|numeric',
            'type' => 'required|in:full-time,part-time,remote,internship',
            'location' => 'nullable|string|max:255',
            'deadline' => 'nullable|date',
            'category_id' => 'nullable|exists:categories,id',
            'is_active' => 'boolean',
        ];
    }
}
