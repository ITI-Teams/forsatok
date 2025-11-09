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

            // Include related user data (optional)
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
            ],
        ];
    }
}

