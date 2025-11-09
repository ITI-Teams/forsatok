<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Location\Models\City;
use App\Domains\Location\Models\Locationable;

class LocationableSeeder extends Seeder
{
    public function run(): void
    {
        $cairo = City::where('name', 'Cairo')->first();
        $alex = City::where('name', 'Alexandria')->first();
        $remote = null;

        $jobs = JobPost::all();

        foreach ($jobs as $index => $job) {
            $city = $index % 2 === 0 ? $cairo : $alex;

            Locationable::firstOrCreate([
                'locationable_id' => $job->id,
                'locationable_type' => JobPost::class,
            ], [
                'country_id' => $city->country_id ?? null,
                'city_id' => $city->id ?? null,
                'address' => $city ? "{$city->name}, {$city->country->name}" : 'Remote',
            ]);
        }
    }
}
