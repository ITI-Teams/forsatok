<?php

namespace App\Domains\Location\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Location\Models\Country;
use App\Domains\Location\Models\City;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    /**
     * Get all countries
     */
    public function getCountries()
    {
        $countries = Country::orderBy('name')->get();

        return response()->json([
            'status' => true,
            'data' => $countries,
        ]);
    }

    /**
     * Get all cities (optionally filtered by country)
     */
    public function getCities(Request $request)
    {
        $query = City::with('country')->orderBy('name');

        if ($countryId = $request->input('country_id')) {
            $query->where('country_id', $countryId);
        }

        $cities = $query->get();

        return response()->json([
            'status' => true,
            'data' => $cities,
        ]);
    }
}

