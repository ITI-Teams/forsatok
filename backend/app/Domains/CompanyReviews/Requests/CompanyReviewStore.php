<?php

namespace App\Domains\CompanyReviews\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CompanyReviewStore extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'company_id' => 'required|exists:employer_infos,id',
            'candidate_id' => 'required|exists:users,id|unique:company_reviews,candidate_id,NULL,id,company_id,' . $this->company_id,
            'rating'       => 'required|integer|min:1|max:5',
            'review'       => 'nullable|string|max:1000',
        ];
    }
}
