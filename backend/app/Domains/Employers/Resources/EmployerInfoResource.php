<?php

namespace App\Domains\Employers\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployerInfoResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */

    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'company_name' => $this->company_name,
            'industry' => $this->industry,
            'about' => $this->about,
            'website' => $this->website,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,

             // Location information
            'location' => $this->when($this->location, function () {
                return [
                    'country' => $this->location->country ? [
                        'id' => $this->location->country->id,
                        'name' => $this->location->country->name,
                        'code' => $this->location->country->code,
                    ] : null,
                    'city' => $this->location->city ? [
                        'id' => $this->location->city->id,
                        'name' => $this->location->city->name,
                    ] : null,
                    'address' => $this->location->address,
                    'full_location' => $this->getFullLocation(),
                ];
            }),

            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ],

            'jobs' => $this->whenLoaded('jobs', function () {
                return $this->jobs->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'experience' => $job->experience,
                        'description' => $job->description,
                        'salary_min' => $job->salary_min,
                        'salary_max' => $job->salary_max,
                        'deadline' => $job->deadline?->format('Y-m-d H:i:s'),
                        'is_active' => (bool) $job->is_active,
                        'created_at' => $job->created_at?->format('Y-m-d H:i:s'),
                    ];
                });
            }),

            'jobs_count' => $this->whenLoaded('jobs', function () {
                return $this->jobs->count();
            }),
        ];
    }

    // Get full location 
    private function getFullLocation(): string
    {
        if (!$this->location) {
            return 'Location not specified';
        }

        $parts = [];

        if ($this->location->address) {
            $parts[] = $this->location->address;
        }

        if ($this->location->city) {
            $parts[] = $this->location->city->name;
        }

        if ($this->location->country) {
            $parts[] = $this->location->country->name;
        }

        return !empty($parts) ? implode(', ', $parts) : 'Location not specified';
    }
}

