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
            'phone' => $this->phone,
            'education' => $this->education,
            'experience' => $this->experience,
            'bio' => $this->bio,
            'resume_url' => $this->resume ? Storage::url($this->resume) : null,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ],
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
        ];
    }
}
