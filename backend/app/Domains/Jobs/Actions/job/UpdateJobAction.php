<?php

namespace App\Domains\Jobs\Actions\job;

use App\Domains\Jobs\Models\JobPost;

class UpdateJobAction
{
    public function execute(JobPost $job_post, array $data)
    {

        $countryId = $data['country_id'] ?? null;
        $cityId = $data['city_id'] ?? null;
        $address = $data['address'] ?? null;

        unset($data['country_id'], $data['city_id'], $data['address']);


        $job_post->update($data);

        if ($countryId && $cityId) {
            if ($job_post->location) {
                $job_post->location->update([
                    'country_id' => $countryId,
                    'city_id' => $cityId,
                    'address' => $address,
                ]);
            } else {
                $job_post->location()->create([
                    'country_id' => $countryId,
                    'city_id' => $cityId,
                    'address' => $address,
                ]);
            }
            return $job_post;
        }
    }
}
