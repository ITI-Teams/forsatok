<?php

namespace App\Domains\Applications\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class ApplayApplicationRequest extends FormRequest
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
            'job_post_id' => ['required', 'integer', 'exists:job_posts,id'],
            'cover_letter' => ['nullable', 'string', 'max:2000'],
            'resume' => ['nullable', 'file', 'mimes:pdf,doc,docx,txt', 'max:5120'],
        ];
    }

    public function messages(): array
    {
        return [
            'job_post_id.required' => 'The job post ID is required.',
            'job_post_id.integer' => 'The job post ID must be an integer.',
            'job_post_id.exists' => 'The selected job post does not exist.',

            'cover_letter.string' => 'The cover letter must be a text.',
            'cover_letter.max' => 'The cover letter may not be greater than 2000 characters.',

            'resume.file' => 'The resume must be a file.',
            'resume.mimes' => 'The resume must be a file of type: pdf, doc, docx, txt.',
            'resume.max' => 'The resume may not be greater than 5MB.',
        ];
    }

    public function validatedPayload(): array
    {
        return $this->validated();
    }
}
