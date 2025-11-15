<?php

namespace App\Domains\Jobs\Controllers\Api ;

use App\Http\Controllers\Controller;
use App\Domains\Jobs\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class JobController extends Controller
{
    public function index(Request $request)
    {
        $query = JobPost::query()
            ->with([
                'category:id,name',
                'employer:id,name,email',
                'locationable.city:id,name,country_id',
                'locationable.city.country:id,name,code',
                'locationable.country:id,name,code'
            ])
            ->where('is_active', true)
            ->latest();

       // Filter by employer_id if provided
        if ($employerId = $request->input('employer_id')) {
            $query->where('employer_id', $employerId);
        }

        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($cityId = $request->input('city_id')) {
            $query->whereHas('locationable', function($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        }

        if ($countryId = $request->input('country_id')) {
            $query->whereHas('locationable.city', function($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }

        if ($location = $request->input('location')) {
            $query->where(function ($locationQuery) use ($location) {
                $locationQuery->whereHas('locationable.city', function ($q) use ($location) {
                    $q->where('name', 'like', "%{$location}%");
                })->orWhereHas('locationable.city.country', function ($q) use ($location) {
                    $q->where('name', 'like', "%{$location}%");
                });
            });
        }

        if ($category = $request->input('category_id')) {
            $query->where('category_id', $category);
        }

        $workTypes = $this->normalizeFilterValues(
            $request->input('work_type') ?? $request->input('type')
        );
        if (!empty($workTypes)) {
            $query->whereIn('work_type', $workTypes);
        }

        $workPlaces = $this->normalizeFilterValues($request->input('work_place'));
        if (!empty($workPlaces)) {
            $query->whereIn('work_place', $workPlaces);
        }

        $experiences = $this->normalizeFilterValues($request->input('experience'));
        if (!empty($experiences)) {
            $query->whereIn('experience', $experiences);
        }

        $minSalary = $request->input('min_salary');
        $maxSalary = $request->input('max_salary');

        if (!is_null($minSalary) || !is_null($maxSalary)) {
            $minSalary = is_null($minSalary) ? null : (float) $minSalary;
            $maxSalary = is_null($maxSalary) ? null : (float) $maxSalary;

            $query->where(function ($salaryQuery) use ($minSalary, $maxSalary) {
                $salaryQuery->where(function ($rangeQuery) use ($minSalary, $maxSalary) {
                    if (!is_null($minSalary) && !is_null($maxSalary)) {
                        $rangeQuery->whereRaw('COALESCE(salary_max, salary_min, 0) >= ?', [$minSalary])
                            ->whereRaw('COALESCE(salary_min, salary_max, 0) <= ?', [$maxSalary]);
                    } elseif (!is_null($minSalary)) {
                        $rangeQuery->whereRaw('COALESCE(salary_max, salary_min, 0) >= ?', [$minSalary]);
                    } elseif (!is_null($maxSalary)) {
                        $rangeQuery->whereRaw('COALESCE(salary_min, salary_max, 0) <= ?', [$maxSalary]);
                    }
                });
            });
        }

        $perPage = $request->input('per_page', 10);
        $jobs = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => $jobs,
        ]);
    }

    public function show($id)
    {
        $job = JobPost::with([
            'category:id,name',
            'employer:id,name,email',
            'locationable.city:id,name,country_id',
            'locationable.city.country:id,name,code',
            'locationable.country:id,name,code',
        ])
            ->where('is_active', true)
            ->findOrFail($id);

        return response()->json([
            'status' => true,
            'data' => $job,
        ]);
    }

    private function normalizeFilterValues(mixed $value): array
    {
        if (is_null($value) || $value === '') {
            return [];
        }

        if (is_string($value)) {
            $value = Str::of($value)
                ->split('/,/')
                ->map(fn ($item) => trim($item))
                ->filter(fn ($item) => $item !== '')
                ->values()
                ->all();
        }

        if (!is_array($value)) {
            $value = [$value];
        }

        return array_values(array_filter(array_map(function ($item) {
            return trim((string) $item);
        }, $value), fn ($item) => $item !== ''));
    }
}
