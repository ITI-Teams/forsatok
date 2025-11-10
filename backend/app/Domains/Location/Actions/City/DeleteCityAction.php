<?php

namespace App\Domains\Location\Actions\City;

use App\Domains\Location\Models\City;

class DeleteCityAction
{
    public function execute(int $cityId): void
    {
        $city = City::onlyTrashed()->findOrFail($cityId);
        $city->forceDelete();
    }
}

