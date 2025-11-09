<?php

namespace App\Domains\Location\Actions\Country;

use App\Domains\Location\Models\Country;

class DeleteCountryAction
{
    public function execute(int $countryId): void
    {
        $country = Country::onlyTrashed()->findOrFail($countryId);
        $country->forceDelete();
    }
}

