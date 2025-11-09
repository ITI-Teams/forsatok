<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Location\Models\City;
use App\Domains\Location\Models\Country;

class CitySeeder extends Seeder
{
    public function run(): void
    {
        $egypt = Country::where('code', 'EG')->first();
        $usa = Country::where('code', 'US')->first();
        $germany = Country::where('code', 'DE')->first();
        $france = Country::where('code', 'FR')->first();
        $uk = Country::where('code', 'UK')->first();

        $cities = [
            ['name' => 'Cairo', 'country_id' => $egypt->id],
            ['name' => 'Alexandria', 'country_id' => $egypt->id],
            ['name' => 'New York', 'country_id' => $usa->id],
            ['name' => 'Berlin', 'country_id' => $germany->id],
            ['name' => 'Paris', 'country_id' => $france->id],
            ['name' => 'London', 'country_id' => $uk->id],
        ];

        foreach ($cities as $city) {
            City::firstOrCreate(
                ['name' => $city['name'], 'country_id' => $city['country_id']],
                $city
            );
        }
    }
}
