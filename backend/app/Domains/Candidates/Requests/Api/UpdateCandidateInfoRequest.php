<?php

namespace App\Domains\Candidates\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Facades\Auth;

class UpdateCandidateInfoRequest extends FormRequest
{
    public function authorize(): bool
    {
        // Only allow authenticated candidates
        return Auth::check() && Auth::user()->type === 'candidate';
    }

    public function rules(): array
    {
        $user = Auth::user();

        return [
            'name' => 'nullable|string|max:255',
            'email' => 'nullable|string|email|max:255|unique:users,email,' . $user->id,
            'password' => 'nullable|string|min:8',
            'phone' => 'nullable|string|max:20',
            'avatar' => 'nullable|file|image|max:2048',
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'education' => 'nullable|string|max:255',
            'experience' => 'nullable|string',
            'bio' => 'nullable|string',
            'job_title' => 'nullable|string|max:255',
            'skills' => 'nullable|array',
            'skills.*' => 'integer|exists:skills,id',
            'gender' => 'nullable|string|in:male,female,other',
            'date_of_birth' => 'nullable|date',
            'country_id' => 'nullable|exists:countries,id',
            'city_id' => 'nullable|exists:cities,id',
            'address' => 'nullable|string|max:255',
            'category_id' => 'sometimes|integer|exists:categories,id',
        ];
    }
}
