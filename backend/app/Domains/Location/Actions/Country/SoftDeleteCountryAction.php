<?php

namespace App\Domains\Location\Actions\Country;

use App\Domains\Location\Models\Country;

class SoftDeleteCountryAction
{
    public function execute(Country $country): void
    {
        $country->delete();
    }
}

