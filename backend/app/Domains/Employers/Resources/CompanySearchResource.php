<?php

namespace App\Domains\Employers\Resources;

use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Storage;

class CompanySearchResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'user_id' => $this->user_id,
            'company_name' => $this->company_name,
            'website' => $this->website,
            'industry' => $this->industry,
            'about' => $this->about,
            'logo_url' => null, // Logo path column doesn't exist in database
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'user' => [
                'id' => $this->user->id ?? null,
                'name' => $this->user->name ?? null,
                'email' => $this->user->email ?? null,
                'avatar' => $this->user->avatar_url ?? null,
            ],
            'jobs_count' => $this->when(isset($this->jobs_count), function () {
                return $this->jobs_count;
            }, function () {
                return $this->jobs()->count();
            }),
            'average_rating' => $this->average_rating,
            'total_reviews' => $this->total_reviews,
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

