<?php

namespace App\Domains\Location\Actions\Country;

use App\Domains\Location\Models\Country;

class RestoreCountryAction
{
    public function execute(int $countryId): void
    {
        $country = Country::onlyTrashed()->findOrFail($countryId);
        $country->restore();
    }
}

