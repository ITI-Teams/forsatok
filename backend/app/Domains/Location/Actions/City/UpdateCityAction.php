<?php

namespace App\Domains\Location\Actions\City;

use App\Domains\Location\Models\City;

class UpdateCityAction
{
    public function execute(City $city, array $data): City
    {
        $city->update($data);
        return $city;
    }
}

