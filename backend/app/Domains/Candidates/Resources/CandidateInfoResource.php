<?php

namespace App\Domains\Candidates\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CandidateInfoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'job_title' => $this->job_title,
            'phone' => $this->phone,
            'education' => $this->education,
            'experience' => $this->experience,
            'bio' => $this->bio,
            'gender' => $this->gender,
            'date_of_birth' => $this->date_of_birth,
            'resume_url' => $this->resume ? Storage::url($this->resume) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'category_id' => $this->category_id,
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
                'avatar' => $this->user->avatar ?? null,
            ],
            'skills' => $this->whenLoaded('skills', function () {
                return $this->skills->pluck('id')->toArray();
            }),
            'skills_details' => $this->whenLoaded('skills', function () {
                return $this->skills->map(function ($skill) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                    ];
                });
            }),
            'location' => $this->location ? [
                'country_id' => $this->location->country_id,
                'city_id' => $this->location->city_id,
                'address' => $this->location->address,
                'country' => $this->location->country ? [
                    'id' => $this->location->country->id,
                    'name' => $this->location->country->name,
                ] : null,
                'city' => $this->location->city ? [
                    'id' => $this->location->city->id,
                    'name' => $this->location->city->name,
                ] : null,
            ] : null,
            'applications' => $this->whenLoaded('applications', function () {
                return $this->applications->map(function ($application) {
                    return [
                        'id' => $application->id,
                        'job_id' => $application->job_id,
                        'status' => $application->status,
                        'applied_at' => $application->created_at?->format('Y-m-d H:i:s'),
                    ];
                });
            }),

            'applications_count' => $this->whenLoaded('applications', function () {
                return $this->applications->count();
            }),
            'location' => $this->whenLoaded('location', function () {
                if ($this->location) {
                    return [
                        'city' => $this->location->city ? [
                            'id' => $this->location->city->id,
                            'name' => $this->location->city->name,
                            'country_id' => $this->location->city->country_id,
                        ] : null,
                        'country' => $this->location->country ? [
                            'id' => $this->location->country->id,
                            'name' => $this->location->country->name,
                            'code' => $this->location->country->code,
                        ] : null,
                    ];
                }
                return null;
            }),
        ];
    }
}
