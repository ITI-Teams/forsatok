<?php

namespace App\Domains\Location\Actions\Country;

use App\Domains\Location\Models\Country;

class CreateCountryAction
{
    public function execute(array $data): Country
    {
        return Country::create($data);
    }
}

