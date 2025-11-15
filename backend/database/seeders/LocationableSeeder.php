<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Location\Models\City;
use App\Domains\Location\Models\Country;
use App\Domains\Location\Models\Locationable;

class LocationableSeeder extends Seeder
{
    public function run(): void
    {
        //-------------------------------------
        // 1) Countries + Their Cities
        //-------------------------------------
        $countries = [
            'Egypt' => [
                'code' => 'EG',
                'cities' => [
                    'Cairo',
                    'Giza',
                    'Alexandria',
                    'Mansoura',
                    'Asyut',
                    'Tanta',
                    'Suez',
                    'Port Said',
                    'Luxor',
                ],
            ],

            'Saudi Arabia' => [
                'code' => 'SA',
                'cities' => [
                    'Riyadh',
                    'Jeddah',
                    'Mecca',
                    'Madinah',
                    'Dammam',
                    'Khobar',
                    'Taif',
                    'Abha',
                ],
            ],

            'United Arab Emirates' => [
                'code' => 'AE',
                'cities' => [
                    'Dubai',
                    'Abu Dhabi',
                    'Sharjah',
                    'Ajman',
                    'Fujairah'
                ],
            ],

            'United States' => [
                'code' => 'US',
                'cities' => [
                    'New York',
                    'Los Angeles',
                    'Chicago',
                    'Houston',
                    'Miami'
                ],
            ],

            'United Kingdom' => [
                'code' => 'UK',
                'cities' => [
                    'London',
                    'Birmingham',
                    'Manchester',
                    'Liverpool',
                    'Leeds'
                ],
            ],
        ];

        $countriesCreated = [];

        //-------------------------------------
        // 2) Seed Countries & Cities
        //-------------------------------------
        foreach ($countries as $countryName => $data) {

            $country = Country::firstOrCreate(
                ['name' => $countryName],
                ['code' => $data['code']]
            );

            $cities = [];

            foreach ($data['cities'] as $cityName) {
                $cities[] = City::firstOrCreate([
                    'name'       => $cityName,
                    'country_id' => $country->id,
                ]);
            }

            $countriesCreated[] = [
                'country' => $country,
                'cities'  => $cities,
            ];
        }

        //-------------------------------------
        // 3) Assign Random City to Each JobPost
        //-------------------------------------
        $jobs = JobPost::all();

        foreach ($jobs as $job) {

            // Pick a random country + random city
            $randCountryData = $countriesCreated[array_rand($countriesCreated)];
            $country         = $randCountryData['country'];
            $city            = $randCountryData['cities'][array_rand($randCountryData['cities'])];

            Locationable::firstOrCreate([
                'locationable_id'   => $job->id,
                'locationable_type' => JobPost::class,
            ], [
                'country_id' => $country->id,
                'city_id'    => $city->id,
                'address'    => "{$city->name}, {$country->name}",
            ]);
        }
    }
}
