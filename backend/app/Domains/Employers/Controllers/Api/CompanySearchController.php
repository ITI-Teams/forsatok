<?php

namespace App\Domains\Employers\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Employers\Models\EmployerInfo;
use App\Domains\Employers\Resources\CompanySearchResource;
use App\Domains\Location\Models\Country;
use App\Domains\Location\Models\City;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CompanySearchController extends Controller
{
    /**
     * Search and filter companies
     */
    public function search(Request $request)
    {
        $query = EmployerInfo::with([
            'user',
            'location.city:id,name,country_id',
            'location.city.country:id,name,code',
            'location.country:id,name,code'
        ])
        ->withCount('jobs')
        ->whereHas('user')
        ->latest();

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('company_name', 'like', "%{$search}%")
                  ->orWhere('about', 'like', "%{$search}%")
                  ->orWhere('industry', 'like', "%{$search}%")
                  ->orWhereHas('user', function($userQuery) use ($search) {
                      $userQuery->where('name', 'like', "%{$search}%")
                                ->orWhere('email', 'like', "%{$search}%");
                  });
            });
        }

        // Location filters
        if ($cityId = $request->input('city_id')) {
            $query->whereHas('location', function($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        }

        if ($countryId = $request->input('country_id')) {
            $query->whereHas('location.city', function($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }

        // Industry filter
        if ($industry = $request->input('industry')) {
            $query->where('industry', 'like', "%{$industry}%");
        }

        $perPage = $request->input('per_page', 10);
        $companies = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => CompanySearchResource::collection($companies),
            'meta' => [
                'current_page' => $companies->currentPage(),
                'last_page' => $companies->lastPage(),
                'per_page' => $companies->perPage(),
                'total' => $companies->total(),
                'from' => $companies->firstItem(),
                'to' => $companies->lastItem(),
            ],
            'message' => 'Companies retrieved successfully.'
        ]);
    }

    /**
     * Get all available filter options for companies
     * Counts are calculated based on currently selected filters (excluding the filter being counted)
     */
    public function getFilterOptions(Request $request)
    {
        // Base query for companies with users (same as search method)
        $baseQuery = EmployerInfo::query()->whereHas('user');

        // Apply selected filters to base query (same logic as search method)
        // Location filters
        if ($cityId = $request->input('selected_city_id')) {
            $baseQuery->whereHas('location', function($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        } elseif ($countryId = $request->input('selected_country_id')) {
            $baseQuery->whereHas('location.city', function($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }

        // Industry filter
        if ($industry = $request->input('selected_industry')) {
            $baseQuery->where('industry', 'like', "%{$industry}%");
        }

        // Get industries with counts
        // For industry counts, we need to exclude the industry filter from baseQuery
        // so that all industries are shown with correct counts
        $industryBaseQuery = EmployerInfo::query()->whereHas('user');

        // Apply all filters except industry to industryBaseQuery
        if ($cityId = $request->input('selected_city_id')) {
            $industryBaseQuery->whereHas('location', function($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        } elseif ($countryId = $request->input('selected_country_id')) {
            $industryBaseQuery->whereHas('location.city', function($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }

        // Don't apply industry filter to industryBaseQuery so all industries are shown

        $industryCounts = (clone $industryBaseQuery)
            ->whereNotNull('industry')
            ->select('industry', DB::raw('COUNT(*) as total'))
            ->groupBy('industry')
            ->pluck('total', 'industry');

        $industries = $industryCounts->map(function ($count, $industry) {
            return [
                'value' => $industry,
                'name' => $industry,
                'count' => (int) $count,
            ];
        })->values()->sortBy('name')->values();

        // Get countries with company counts
        $countries = Country::orderBy('name')->get()->map(function ($country) use ($baseQuery) {
            $count = (clone $baseQuery)
                ->whereHas('location', function($q) use ($country) {
                    $q->where('country_id', $country->id);
                })
                ->count();

            return [
                'id' => $country->id,
                'name' => $country->name,
                'code' => $country->code,
                'companies_count' => (int) $count,
            ];
        });

        // Get cities with company counts
        $cities = City::with('country')->orderBy('name')->get()->map(function ($city) use ($baseQuery) {
            $count = (clone $baseQuery)
                ->whereHas('location', function($q) use ($city) {
                    $q->where('city_id', $city->id);
                })
                ->count();

            return [
                'id' => $city->id,
                'name' => $city->name,
                'country_id' => $city->country_id,
                'country' => $city->country ? [
                    'id' => $city->country->id,
                    'name' => $city->country->name,
                    'code' => $city->country->code,
                ] : null,
                'companies_count' => (int) $count,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'industries' => $industries,
                'countries' => $countries,
                'cities' => $cities,
            ]
        ]);
    }
}

