<?php

namespace App\Domains\Jobs\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class JobPostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'requirements' => $this->requirements,
            'responsibilities' => $this->responsibilities,
            'location' => $this->location,
            'salary' => $this->salary,
            'employment_type' => $this->employment_type,
            'experience_level' => $this->experience_level,
            'deadline' => $this->deadline,
            'status' => $this->status,
            'employer' => $this->employer ? [
                'id' => $this->employer->id,
                'name' => $this->employer->name,
                'company' => $this->employer->company_name,
                'email' => $this->employer->email,
            ] : null,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ] : null,
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'human_readable_posted_date' => $this->created_at->diffForHumans(),
            'days_remaining' => now()->diffInDays($this->deadline, false),
        ];
    }
}
