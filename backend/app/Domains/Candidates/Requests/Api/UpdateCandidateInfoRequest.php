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
            'resume' => 'nullable|file|mimes:pdf,doc,docx|max:2048',
            'education' => 'nullable|string|max:255',
            'experience' => 'nullable|string',
            'bio' => 'nullable|string',
        ];
    }
}
