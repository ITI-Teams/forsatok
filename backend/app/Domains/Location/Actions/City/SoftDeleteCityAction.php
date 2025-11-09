<?php

namespace App\Domains\Location\Actions\City;

use App\Domains\Location\Models\City;

class SoftDeleteCityAction
{
    public function execute(City $city): void
    {
        $city->delete();
    }
}

