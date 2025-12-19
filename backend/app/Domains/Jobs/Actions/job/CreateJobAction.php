<?php

namespace App\Domains\Jobs\Actions\job;

use App\Domains\Jobs\Models\JobPost;
use App\Domains\Shared\Models\AuditLog;
use Illuminate\Support\Facades\Auth;

class CreateJobAction
{
    public function execute(array $data): JobPost
    {
        $data['employer_id'] = Auth::id();
        $countryId = $data['country_id'] ?? null;
        $cityId = $data['city_id'] ?? null;
        $address = $data['address'] ?? null;
        $skills = $data['skills'] ?? [];
        unset($data['country_id'], $data['city_id'], $data['address'], $data['skills']);

        $job = JobPost::create($data);

        if ($countryId && $cityId) {
            $job->location()->create([
                'country_id' => $countryId,
                'city_id' => $cityId,
                'address' => $address,
            ]);
        }

        if (!empty($skills)) {
            $job->skills()->attach($skills);
        }

        AuditLog::create([
            'user_id' => auth()->id(),
            'action' => 'created_job',
            'model_type' => JobPost::class,
            'model_id' => $job->id,
            'changes' => $job->title,
            'ip_address' => request()->ip(),
        ]);

        return $job;
    }
}
