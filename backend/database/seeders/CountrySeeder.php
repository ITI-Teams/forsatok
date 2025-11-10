<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Domains\Location\Models\Country;

class CountrySeeder extends Seeder
{
    public function run(): void
    {
        $countries = [
            ['name' => 'Egypt', 'code' => 'EG'],
            ['name' => 'United States', 'code' => 'US'],
            ['name' => 'Germany', 'code' => 'DE'],
            ['name' => 'France', 'code' => 'FR'],
            ['name' => 'United Kingdom', 'code' => 'UK'],
        ];

        foreach ($countries as $data) {
            Country::firstOrCreate(['code' => $data['code']], $data);
        }
    }
}
