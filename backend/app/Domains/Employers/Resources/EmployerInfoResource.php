<?php

namespace App\Domains\Employers\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;
use Carbon\Carbon;


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
                'avatar' => $this->user->avatar ?? null,
            ],

            'average_rating' => $this->average_rating,
            'total_reviews' => $this->total_reviews,
            'reviews' => $this->whenLoaded('reviews', function () {
                return $this->reviews->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'rating' => $review->rating,
                        'review' => $review->review,
                        'created_at' => $review->created_at->format('Y-m-d H:i:s'),
                        'candidate' => [
                            'id' => $review->candidate->id,
                            'name' => $review->candidate->name,
                        ],
                    ];
                });
            }),

            'jobs' => $this->whenLoaded('jobs', function () {
                return $this->jobs->map(function ($job) {
                    return [
                        'id' => $job->id,
                        'title' => $job->title,
                        'experience' => $job->experience,
                        'description' => $job->description,
                        'salary_min' => $job->salary_min,
                        'salary_max' => $job->salary_max,
                        'deadline' => $this->formatDate($job->deadline),
                        'is_active' => (bool) $job->is_active,
                        'created_at' => $this->formatDate($job->created_at),
                        'work_type' => $job->work_type,
                        'work_place' => $job->work_place,
                    ];
                });
            }),

            'jobs_count' => $this->whenLoaded('jobs', function () {
                return $this->jobs->count();
            }),
        ];
    }


//  Safely format a date - handles both Carbon instances and strings

    private function formatDate($date): ?string
    {
        if (!$date) {
            return null;
        }

        // If it's already a string, return it as is
        if (is_string($date)) {
            return $date;
        }

        // If it's a Carbon instance, format it
        if ($date instanceof \Carbon\Carbon || $date instanceof \DateTime) {
            return $date->format('Y-m-d H:i:s');
        }

        // Try to parse as Carbon if it's something else
        try {
            return Carbon::parse($date)->format('Y-m-d H:i:s');
        } catch (\Exception $e) {
            return null;
        }
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
