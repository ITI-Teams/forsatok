<?php

namespace App\Domains\Jobs\Requests\Skill;

use Illuminate\Foundation\Http\FormRequest;

class UpdateSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'skill_id'    => 'required|exists:skills,id',
            'name'        => 'required|string|max:255|unique:skills,name,' . $this->skill_id,
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
