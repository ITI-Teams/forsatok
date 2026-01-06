<?php

namespace App\Domains\Jobs\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Jobs\Models\JobPost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class JobSkillSearchController extends Controller
{
    /**
     * Search jobs by skills with OR logic and relevance scoring
     * Jobs with ANY of the selected skills will be shown,
     * ordered by how many matching skills they have (most matches first)
     */
    public function searchBySkills(Request $request)
    {
        $request->validate([
            'skill_ids' => 'required|array|min:1',
            'skill_ids.*' => 'integer|exists:skills,id',
            'per_page' => 'nullable|integer|min:1|max:100',
        ]);

        $skillIds = $request->input('skill_ids');
        $perPage = $request->input('per_page', 10);

        // Base query with relationships
        $query = JobPost::with([
            'category:id,name,slug',
            'employer:id,name,email,avatar',
            'skills',
            'locationable.city:id,name,country_id',
            'locationable.city.country:id,name,code',
            'locationable.country:id,name,code'
        ]);

        $user = auth('sanctum')->user();
        if (!$user || $user->hasRole('candidate')) {
            $query->availableJobs();
        } else {
            $query->where('is_active', true);
        }

        // Filter jobs that have AT LEAST ONE of the selected skills (OR logic)
        $query->whereHas('skills', function($q) use ($skillIds) {
            $q->whereIn('skills.id', $skillIds);
        });

        // Add a subquery to count matching skills for sorting
        $query->addSelect([
            'job_posts.*',
            'matching_skills_count' => DB::table('job_skills')
                ->selectRaw('COUNT(*)')
                ->whereColumn('job_skills.job_id', 'job_posts.id')
                ->whereIn('job_skills.skill_id', $skillIds)
        ]);

        // Order by matching skills count (descending) - jobs with more matches appear first
        $query->orderByDesc('matching_skills_count')
              ->orderByDesc('job_posts.created_at'); // Secondary sort by newest

        // Paginate results
        $jobs = $query->paginate($perPage);

        // Add relevance metrics to each job
        $jobsWithRelevance = $jobs->getCollection()->map(function ($job) use ($skillIds) {
            $matchingCount = $job->matching_skills_count ?? 0;
            $totalSkills = count($skillIds);

            return [
                'id' => $job->id,
                'title' => $job->title,
                'description' => $job->description,
                'responsibilities' => $job->responsibilities ?? null,
                'qualification' => $job->qualification ?? null,
                'benefits' => $job->benefits ?? null,
                'experience' => $job->experience,
                'salary_min' => $job->salary_min,
                'salary_max' => $job->salary_max,
                'work_type' => $job->work_type,
                'work_place' => $job->work_place,
                'deadline' => $job->deadline,
                'is_active' => $job->is_active,
                'views' => $job->views ?? 0,

                // Employer information
                'employer' => [
                    'id' => $job->employer->id,
                    'name' => $job->employer->name,
                    'email' => $job->employer->email,
                    'avatar' => $job->employer->avatar ?? null,
                ],

                // Category
                'category' => $job->category ? [
                    'id' => $job->category->id,
                    'name' => $job->category->name,
                    'slug' => $job->category->slug,
                ] : null,

                // Skills with indication of which ones match
                'skills' => $job->skills->map(function ($skill) use ($skillIds) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'slug' => $skill->slug,
                        'is_matching' => in_array($skill->id, $skillIds), // Indicates if this skill was in the search
                    ];
                }),

                // Location
                'location' => $job->locationable ? [
                    'country' => $job->locationable->country ? [
                        'id' => $job->locationable->country->id,
                        'name' => $job->locationable->country->name,
                        'code' => $job->locationable->country->code,
                    ] : null,
                    'city' => $job->locationable->city ? [
                        'id' => $job->locationable->city->id,
                        'name' => $job->locationable->city->name,
                        'country_id' => $job->locationable->city->country_id,
                    ] : null,
                    'address' => $job->locationable->address ?? null,
                ] : null,

                // Relevance metrics
                'matching_skills_count' => $matchingCount,
                'total_searched_skills' => $totalSkills,
                'relevance_percentage' => $totalSkills > 0
                    ? round(($matchingCount / $totalSkills) * 100, 2)
                    : 0,
                'relevance_label' => $this->getRelevanceLabel($matchingCount, $totalSkills),

                'created_at' => $job->created_at,
                'updated_at' => $job->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $jobsWithRelevance,
            'meta' => [
                'current_page' => $jobs->currentPage(),
                'last_page' => $jobs->lastPage(),
                'per_page' => $jobs->perPage(),
                'total' => $jobs->total(),
                'from' => $jobs->firstItem(),
                'to' => $jobs->lastItem(),
            ],
            'search_criteria' => [
                'skill_ids' => $skillIds,
                'total_skills_searched' => count($skillIds),
            ],
            'message' => 'Jobs retrieved successfully with skill relevance scoring.'
        ]);
    }

    /**
     * Get a human-readable relevance label
     */
    private function getRelevanceLabel(int $matchingCount, int $totalCount): string
    {
        if ($totalCount === 0) {
            return 'N/A';
        }

        $percentage = ($matchingCount / $totalCount) * 100;

        if ($percentage === 100) {
            return 'Perfect Match';
        } elseif ($percentage >= 75) {
            return 'Excellent Match';
        } elseif ($percentage >= 50) {
            return 'Good Match';
        } elseif ($percentage >= 25) {
            return 'Partial Match';
        } else {
            return 'Low Match';
        }
    }

    /**
     * Get skill match statistics for jobs
     * Returns breakdown of how many jobs match different skill counts
     */
    public function getSkillMatchStats(Request $request)
    {
        $request->validate([
            'skill_ids' => 'required|array|min:1',
            'skill_ids.*' => 'integer|exists:skills,id',
        ]);

        $skillIds = $request->input('skill_ids');

        // Base query
        $baseQuery = JobPost::query();

        $user = auth('sanctum')->user();
        if (!$user || $user->hasRole('candidate')) {
            $baseQuery->availableJobs();
        } else {
            $baseQuery->where('is_active', true);
        }

        $baseQuery->whereHas('skills', function($q) use ($skillIds) {
            $q->whereIn('skills.id', $skillIds);
        });

        // Get jobs with matching skill counts
        $jobs = $baseQuery
            ->addSelect([
                'job_posts.*',
                'matching_skills_count' => DB::table('job_skills')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('job_skills.job_id', 'job_posts.id')
                    ->whereIn('job_skills.skill_id', $skillIds)
            ])
            ->get();

        // Group by matching skills count
        $statistics = $jobs->groupBy('matching_skills_count')
            ->map(function ($group, $count) use ($skillIds) {
                $percentage = count($skillIds) > 0
                    ? round(($count / count($skillIds)) * 100, 2)
                    : 0;

                return [
                    'matching_skills_count' => (int) $count,
                    'jobs_count' => $group->count(),
                    'percentage_of_searched_skills' => $percentage,
                    'label' => $this->getRelevanceLabel((int) $count, count($skillIds)),
                ];
            })
            ->sortByDesc('matching_skills_count')
            ->values();

        $totalJobs = $jobs->count();
        $totalSkillsSearched = count($skillIds);

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $statistics,
                'summary' => [
                    'total_jobs_found' => $totalJobs,
                    'total_skills_searched' => $totalSkillsSearched,
                    'perfect_matches' => $statistics->where('matching_skills_count', $totalSkillsSearched)->sum('jobs_count'),
                    'partial_matches' => $statistics->where('matching_skills_count', '<', $totalSkillsSearched)->sum('jobs_count'),
                ],
            ],
            'message' => 'Job skill match statistics retrieved successfully.'
        ]);
    }
}
