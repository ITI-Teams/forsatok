<?php

namespace App\Domains\Jobs\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class SaveJobRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return auth()->check() && auth()->user()->type === 'candidate';
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'job_post_id' => ['required', 'integer', 'exists:job_posts,id'],
        ];
    }

    public function messages(): array
    {
        return [
            'job_post_id.required' => 'Job id is required.',
            'job_post_id.exists' => 'Job not found.',
        ];
    }
}
