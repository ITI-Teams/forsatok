<?php

namespace App\Domains\Location\Actions\City;

use App\Domains\Location\Models\City;

class CreateCityAction
{
    public function execute(array $data): City
    {
        return City::create($data);
    }
}

