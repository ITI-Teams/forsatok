<?php

namespace App\Domains\Applications\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreApplicationRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'candidate_id' => 'required|exists:users,id',
            'job_post_id' => 'required|exists:job_posts,id',
            'cover_letter' => 'nullable|string',
            'resume_path' => 'nullable|string',
            'status' => 'required|in:pending,accepted,rejected',
        ];
    }
}
