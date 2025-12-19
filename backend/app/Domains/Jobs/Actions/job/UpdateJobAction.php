<?php

namespace App\Domains\Jobs\Actions\job;

use App\Domains\Jobs\Models\JobPost;
use App\Domains\Shared\Models\AuditLog;

class UpdateJobAction
{
    public function execute(JobPost $job_post, array $data)
    {

        $countryId = $data['country_id'] ?? null;
        $cityId = $data['city_id'] ?? null;
        $address = $data['address'] ?? null;
        $skills = $data['skills'] ?? null;

        unset($data['country_id'], $data['city_id'], $data['address'], $data['skills']);


        $job_post->update($data);

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'created_job',
            'model_type' => JobPost::class,
            'model_id' => $job_post->id,
            'changes' => $job_post->title,
            'ip_address' => request()->ip(),
        ]);

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
        }

        if ($skills !== null) {
            $job_post->skills()->sync($skills);
        }

        return $job_post;
    }
}
