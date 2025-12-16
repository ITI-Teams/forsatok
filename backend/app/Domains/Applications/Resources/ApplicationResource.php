<?php

namespace App\Domains\Applications\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class ApplicationResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'candidate' => $this->candidate ? [
                'id' => $this->candidate->id,
                'name' => $this->candidate->name,
                'email' => $this->candidate->email,
                'phone' => $this->candidate->candidateInfo->phone ?? null
            ] : null,
            'job_post' => $this->jobPost ? [
                'id' => $this->jobPost->id,
                'title' => $this->jobPost->title,
                'description' => $this->jobPost->description,
                'location' => $this->jobPost->location,
                'salary' => $this->jobPost->salary,
                'deadline' => $this->jobPost->deadline,
                'employment_type' => $this->jobPost->employment_type,
                'experience_level' => $this->jobPost->experience_level,
                'employer' => $this->jobPost->employer ? [
                    'id' => $this->jobPost->employer->id,
                    'name' => $this->jobPost->employer->name,
                    'company' => $this->jobPost->employer->company_name,
                    'email' => $this->jobPost->employer->email,
                ] : null,
                'category' => $this->jobPost->category ? [
                    'id' => $this->jobPost->category->id,
                    'name' => $this->jobPost->category->name,
                ] : null,
                'skills' => $this->jobPost->skills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'slug' => $skill->slug,
                    ];
                }),
            ] : null,
            'cover_letter' => $this->cover_letter,
            'resume_url' => $this->resume_path ? Storage::url($this->resume_path) : null,
            'resume_filename' => $this->resume_path ? basename($this->resume_path) : null,
            'status' => $this->status,
            'applied_date' => $this->created_at?->toDateTimeString(),
            'updated_at' => $this->updated_at?->toDateTimeString(),
            'human_readable_applied_date' => $this->created_at?->diffForHumans(),
        ];
    }
}
