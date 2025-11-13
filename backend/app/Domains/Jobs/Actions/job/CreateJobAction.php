<?php

namespace App\Domains\Jobs\Actions\Job;

use App\Domains\Jobs\Models\JobPost;
use Illuminate\Support\Facades\Auth;

class CreateJobAction
{
    public function execute(array $data): JobPost
    {
        $data['employer_id'] = Auth::id();
        $countryId = $data['country_id'] ?? null;
        $cityId = $data['city_id'] ?? null;
        $address = $data['address'] ?? null;
        unset($data['country_id'], $data['city_id'],$data['address']);

        $job = JobPost::create($data);

        if ($countryId && $cityId) {
            $job->location()->create([
                'country_id' => $countryId,
                'city_id' => $cityId,
                'address' => $address,
            ]);
        }

        return $job;
    }
}
