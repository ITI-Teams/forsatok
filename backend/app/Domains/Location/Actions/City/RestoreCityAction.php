<?php

namespace App\Domains\Location\Actions\City;

use App\Domains\Location\Models\City;

class RestoreCityAction
{
    public function execute(int $cityId): void
    {
        $city = City::onlyTrashed()->findOrFail($cityId);
        $city->restore();
    }
}

