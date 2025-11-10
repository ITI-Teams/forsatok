<?php

namespace App\Domains\Location\Actions\Country;

use App\Domains\Location\Models\Country;

class UpdateCountryAction
{
    public function execute(Country $country, array $data): Country
    {
        $country->update($data);
        return $country;
    }
}

