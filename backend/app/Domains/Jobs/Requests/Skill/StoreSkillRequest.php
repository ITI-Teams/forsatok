<?php

namespace App\Domains\Jobs\Requests\Skill;

use Illuminate\Foundation\Http\FormRequest;

class StoreSkillRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name'        => 'required|string|max:255|unique:skills,name',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
