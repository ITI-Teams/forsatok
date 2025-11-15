<?php

namespace App\Domains\Candidates\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Candidates\Models\CandidateInfo;
use App\Domains\Candidates\Resources\CandidateInfoResource;
use App\Domains\Jobs\Models\Skill;
use App\Domains\Location\Models\Country;
use App\Domains\Location\Models\City;
use App\Domains\Location\Models\Locationable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

class CandidateSearchController extends Controller
{
    /**
     * Search and filter candidates
     */
    public function search(Request $request)
    {
        $query = CandidateInfo::with([
            'user',
            'skills',
            'location.city:id,name,country_id',
            'location.city.country:id,name,code',
            'location.country:id,name,code'
        ])
        ->whereHas('user')
        ->latest();

        // Exclude logged-in user if authenticated
        if ($user = $request->user('sanctum')) {
            $query->where('user_id', '!=', $user->id);
        }

        // Search filter
        if ($search = $request->input('search')) {
            $query->where(function($q) use ($search) {
                $q->whereHas('user', function($userQuery) use ($search) {
                    $userQuery->where('name', 'like', "%{$search}%")
                        ->orWhere('email', 'like', "%{$search}%");
                })
                ->orWhere('bio', 'like', "%{$search}%")
                ->orWhere('job_title', 'like', "%{$search}%");
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

        // Skills filter (AND logic - candidate must have ALL selected skills)
        $skillIds = $this->normalizeFilterValues($request->input('skill_ids'));
        if (!empty($skillIds)) {
            foreach ($skillIds as $skillId) {
                $query->whereHas('skills', function($q) use ($skillId) {
                    $q->where('skills.id', $skillId);
                });
            }
        }

        // Education filter
        if ($education = $request->input('education')) {
            $query->where('education', 'like', "%{$education}%");
        }

        // Experience filter
        $experiences = $this->normalizeFilterValues($request->input('experience'));
        if (!empty($experiences)) {
            $query->whereIn('experience', $experiences);
        }

        // Experience range filter
        if ($minExperience = $request->input('min_experience')) {
            $query->whereRaw('CAST(experience AS UNSIGNED) >= ?', [(int)$minExperience]);
        }
        if ($maxExperience = $request->input('max_experience')) {
            $query->whereRaw('CAST(experience AS UNSIGNED) <= ?', [(int)$maxExperience]);
        }

        $perPage = $request->input('per_page', 10);
        $candidates = $query->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => CandidateInfoResource::collection($candidates),
            'meta' => [
                'current_page' => $candidates->currentPage(),
                'last_page' => $candidates->lastPage(),
                'per_page' => $candidates->perPage(),
                'total' => $candidates->total(),
                'from' => $candidates->firstItem(),
                'to' => $candidates->lastItem(),
            ],
            'message' => 'Candidates retrieved successfully.'
        ]);
    }

    /**
     * Get all available filter options for candidates
     * Counts are calculated based on currently selected filters (excluding the filter being counted)
     */
    public function getFilterOptions(Request $request)
    {
        // Get all skills with candidate counts (use LEFT JOIN to include all skills)
        $allSkills = Skill::orderBy('name')->get();
        
        // Base query for candidates with users (same as search method)
        $baseQuery = CandidateInfo::query()->whereHas('user');

        // Exclude logged-in user if authenticated
        if ($user = $request->user('sanctum')) {
            $baseQuery->where('user_id', '!=', $user->id);
        }
        
        // Apply selected filters to base query (same logic as search method)
        // Skills filter
        $selectedSkillIds = $this->normalizeFilterValues($request->input('selected_skill_ids'));
        if (!empty($selectedSkillIds)) {
            foreach ($selectedSkillIds as $skillId) {
                $baseQuery->whereHas('skills', function($q) use ($skillId) {
                    $q->where('skills.id', $skillId);
                });
            }
        }
        
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
        
        // Education filter
        if ($education = $request->input('selected_education')) {
            $baseQuery->where('education', 'like', "%{$education}%");
        }
        
        // Experience filter
        $selectedExperiences = $this->normalizeFilterValues($request->input('selected_experience'));
        if (!empty($selectedExperiences)) {
            $baseQuery->whereIn('experience', $selectedExperiences);
        }
        
        $skillCounts = $allSkills->map(function ($skill) use ($baseQuery) {
            $count = (clone $baseQuery)
                ->whereHas('skills', function($q) use ($skill) {
                    $q->where('skills.id', $skill->id);
                })
                ->count();
            
            return [
                'id' => $skill->id,
                'name' => $skill->name,
                'slug' => $skill->slug,
                'count' => (int) $count,
            ];
        });

        // Get education levels with counts (using baseQuery to respect selected skills)
        $educationCounts = (clone $baseQuery)
            ->whereNotNull('education')
            ->select('education', DB::raw('COUNT(*) as total'))
            ->groupBy('education')
            ->pluck('total', 'education');

        $educationLevels = collect([
            'High School',
            'Bachelor\'s Degree',
            'Master\'s Degree',
            'PhD',
            'Diploma',
            'Certificate'
        ])->map(function ($level) use ($educationCounts) {
            return [
                'value' => $level,
                'name' => $level,
                'count' => (int) ($educationCounts[$level] ?? 0),
            ];
        })->merge(
            $educationCounts->keys()->filter(function ($key) {
                return !in_array($key, ['High School', 'Bachelor\'s Degree', 'Master\'s Degree', 'PhD', 'Diploma', 'Certificate']);
            })->map(function ($level) use ($educationCounts) {
                return [
                    'value' => $level,
                    'name' => $level,
                    'count' => (int) ($educationCounts[$level] ?? 0),
                ];
            })
        )->filter(function ($item) {
            return $item['count'] > 0;
        })->values();

        // Get experience levels with counts
        // For experience counts, we need to exclude the experience filter from baseQuery
        // so that all experience levels are shown with correct counts
        $experienceBaseQuery = CandidateInfo::query()->whereHas('user');

        // Exclude logged-in user if authenticated
        if ($user = $request->user('sanctum')) {
            $experienceBaseQuery->where('user_id', '!=', $user->id);
        }
        
        // Apply all filters except experience to experienceBaseQuery
        $selectedSkillIds = $this->normalizeFilterValues($request->input('selected_skill_ids'));
        if (!empty($selectedSkillIds)) {
            foreach ($selectedSkillIds as $skillId) {
                $experienceBaseQuery->whereHas('skills', function($q) use ($skillId) {
                    $q->where('skills.id', $skillId);
                });
            }
        }
        
        if ($cityId = $request->input('selected_city_id')) {
            $experienceBaseQuery->whereHas('location', function($q) use ($cityId) {
                $q->where('city_id', $cityId);
            });
        } elseif ($countryId = $request->input('selected_country_id')) {
            $experienceBaseQuery->whereHas('location.city', function($q) use ($countryId) {
                $q->where('country_id', $countryId);
            });
        }
        
        if ($education = $request->input('selected_education')) {
            $experienceBaseQuery->where('education', 'like', "%{$education}%");
        }
        
        // Don't apply experience filter to experienceBaseQuery so all levels are shown
        
        $experienceCounts = (clone $experienceBaseQuery)
            ->whereNotNull('experience')
            ->select('experience', DB::raw('COUNT(*) as total'))
            ->groupBy('experience')
            ->pluck('total', 'experience');

        $experienceLevels = collect([
            'Entry Level',
            '1-3 years',
            '3-5 years',
            '5+ years',
            'Senior'
        ])->map(function ($level) use ($experienceCounts) {
            return [
                'value' => $level,
                'name' => $level,
                'count' => (int) ($experienceCounts[$level] ?? 0),
            ];
        })->merge(
            $experienceCounts->keys()->filter(function ($key) {
                return !in_array($key, ['Entry Level', '1-3 years', '3-5 years', '5+ years', 'Senior']);
            })->map(function ($level) use ($experienceCounts) {
                return [
                    'value' => $level,
                    'name' => $level,
                    'count' => (int) ($experienceCounts[$level] ?? 0),
                ];
            })
        )->filter(function ($item) {
            return $item['count'] > 0;
        })->values();

        // Map experience levels to min/max years for filtering
        $experienceLevelsWithRange = $experienceLevels->map(function ($level) {
            $min = 0;
            $max = 0;
            
            switch ($level['value']) {
                case 'Entry Level':
                    $min = 0;
                    $max = 1;
                    break;
                case '1-3 years':
                    $min = 1;
                    $max = 3;
                    break;
                case '3-5 years':
                    $min = 3;
                    $max = 5;
                    break;
                case '5+ years':
                    $min = 5;
                    $max = 20;
                    break;
                case 'Senior':
                    $min = 10;
                    $max = 20;
                    break;
                default:
                    // Try to extract numbers from string like "5-10 years"
                    if (preg_match('/(\d+)\s*-\s*(\d+)/', $level['value'], $matches)) {
                        $min = (int) $matches[1];
                        $max = (int) $matches[2];
                    } elseif (preg_match('/(\d+)\+/', $level['value'], $matches)) {
                        $min = (int) $matches[1];
                        $max = 20;
                    } elseif (preg_match('/(\d+)/', $level['value'], $matches)) {
                        $min = (int) $matches[1];
                        $max = (int) $matches[1];
                    }
                    break;
            }
            
            return [
                'value' => $level['value'],
                'name' => $level['name'],
                'count' => $level['count'],
                'min' => $min,
                'max' => $max,
            ];
        });

        // Get countries with candidate counts (using same base query)
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
                'candidates_count' => (int) $count,
            ];
        });

        // Get cities with candidate counts (using same base query)
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
                'candidates_count' => (int) $count,
            ];
        });

        return response()->json([
            'status' => true,
            'data' => [
                'skills' => $skillCounts,
                'education_levels' => $educationLevels,
                'experience_levels' => $experienceLevelsWithRange,
                'countries' => $countries,
                'cities' => $cities,
            ]
        ]);
    }

    /**
     * Normalize filter values (handle comma-separated strings, arrays, etc.)
     */
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
            $trimmed = trim((string) $item);
            // Convert to integer if it's a numeric string
            return is_numeric($trimmed) ? (int) $trimmed : $trimmed;
        }, $value), fn ($item) => $item !== '' && $item !== null));
    }
}
