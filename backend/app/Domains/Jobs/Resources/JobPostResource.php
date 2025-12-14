<?php

namespace App\Domains\Jobs\Resources;

use Carbon\Carbon;
use Illuminate\Http\Resources\Json\JsonResource;

class JobPostResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'experience' => $this->experience,
            'responsibilities' => $this->responsibilities,
            'qualifications' => $this->qualification,
            'benefits' => $this->benefits,
            'location' => $this->location ? [
                'country' => [
                    'id' => $this->location->country_id,
                    'name' => $this->location->country->name ?? null,
                ],
                'city' => [
                    'id' => $this->location->city_id,
                    'name' => $this->location->city->name ?? null,
                ],
                'address' => $this->location->address,
            ] : null,
            'salary' => [
                'min' => $this->salary_min,
                'max' => $this->salary_max,
                'formatted' => $this->salary_min && $this->salary_max
                    ? number_format($this->salary_min, 2) . ' - ' . number_format($this->salary_max, 2)
                    : null,
            ],
            'work_type' => $this->work_type,
            'work_place' => $this->work_place,
            'deadline' => $this->deadline ? Carbon::parse($this->deadline)->toDateTimeString() : null,
            'days_remaining' => $this->deadline ? now()->diffInDays(Carbon::parse($this->deadline), false) : null,
            'is_active' => $this->is_active,
            'employer' => $this->employer ? [
                'id' => $this->employer->id,
                'name' => $this->employer->name,
                'company' => $this->employer->company_name ?? null,
                'email' => $this->employer->email,
            ] : null,
            'category' => $this->category ? [
                'id' => $this->category->id,
                'name' => $this->category->name,
            ] : null,
            'skills' => $this->skills->map(function ($skill) {
                return [
                    'id' => $skill->id,
                    'name' => $skill->name,
                    'slug' => $skill->slug,
                ];
            }),
            'created_at' => $this->created_at->toDateTimeString(),
            'updated_at' => $this->updated_at->toDateTimeString(),
            'human_readable_posted_date' => $this->created_at->diffForHumans(),
        ];
    }
}
