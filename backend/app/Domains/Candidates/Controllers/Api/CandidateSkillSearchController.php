<?php

namespace App\Domains\Candidates\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Domains\Candidates\Models\CandidateInfo;
use App\Domains\Candidates\Resources\CandidateInfoResource;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CandidateSkillSearchController extends Controller
{
    /**
     * Search candidates by skills with OR logic and relevance scoring
     * Candidates with ANY of the selected skills will be shown,
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
        $query = CandidateInfo::with([
            'user',
            'skills',
            'location.city:id,name,country_id',
            'location.city.country:id,name,code',
            'location.country:id,name,code'
        ])
        ->whereHas('user');

        // Exclude logged-in user if authenticated
        if ($user = $request->user('sanctum')) {
            $query->where('user_id', '!=', $user->id);
        }

        // Filter candidates who have AT LEAST ONE of the selected skills (OR logic)
        $query->whereHas('skills', function($q) use ($skillIds) {
            $q->whereIn('skills.id', $skillIds);
        });

        // Add a subquery to count matching skills for sorting
        $query->addSelect([
            'candidate_infos.*',
            'matching_skills_count' => DB::table('candidate_skill')
                ->selectRaw('COUNT(*)')
                ->whereColumn('candidate_skill.candidate_info_id', 'candidate_infos.id')
                ->whereIn('candidate_skill.skill_id', $skillIds)
        ]);

        // Order by matching skills count (descending) - candidates with more matches appear first
        $query->orderByDesc('matching_skills_count')
              ->orderByDesc('candidate_infos.created_at'); // Secondary sort by newest

        // Paginate results
        $candidates = $query->paginate($perPage);

        // Add relevance metrics to each candidate
        $candidatesWithRelevance = $candidates->getCollection()->map(function ($candidate) use ($skillIds) {
            $matchingCount = $candidate->matching_skills_count ?? 0;
            $totalSkills = count($skillIds);

            return [
                'id' => $candidate->id,
                'user_id' => $candidate->user_id,
                'job_title' => $candidate->job_title,
                'phone' => $candidate->phone,
                'resume' => $candidate->resume,
                'education' => $candidate->education,
                'experience' => $candidate->experience,
                'bio' => $candidate->bio,
                'gender' => $candidate->gender,
                'date_of_birth' => $candidate->date_of_birth,
                'category_id' => $candidate->category_id,

                // User information
                'user' => [
                    'id' => $candidate->user->id,
                    'name' => $candidate->user->name,
                    'email' => $candidate->user->email,
                    'avatar' => $candidate->user->avatar ?? null,
                ],

                // Skills with indication of which ones match
                'skills' => $candidate->skills->map(function ($skill) use ($skillIds) {
                    return [
                        'id' => $skill->id,
                        'name' => $skill->name,
                        'slug' => $skill->slug,
                        'is_matching' => in_array($skill->id, $skillIds), // Indicates if this skill was in the search
                    ];
                }),

                // Location
                'location' => $candidate->location ? [
                    'country' => $candidate->location->country ? [
                        'id' => $candidate->location->country->id,
                        'name' => $candidate->location->country->name,
                        'code' => $candidate->location->country->code,
                    ] : null,
                    'city' => $candidate->location->city ? [
                        'id' => $candidate->location->city->id,
                        'name' => $candidate->location->city->name,
                        'country_id' => $candidate->location->city->country_id,
                    ] : null,
                    'address' => $candidate->location->address ?? null,
                ] : null,

                // Category
                'category' => $candidate->category ? [
                    'id' => $candidate->category->id,
                    'name' => $candidate->category->name,
                    'slug' => $candidate->category->slug,
                ] : null,

                // Relevance metrics
                'matching_skills_count' => $matchingCount,
                'total_searched_skills' => $totalSkills,
                'relevance_percentage' => $totalSkills > 0
                    ? round(($matchingCount / $totalSkills) * 100, 2)
                    : 0,
                'relevance_label' => $this->getRelevanceLabel($matchingCount, $totalSkills),

                'created_at' => $candidate->created_at,
                'updated_at' => $candidate->updated_at,
            ];
        });

        return response()->json([
            'success' => true,
            'data' => $candidatesWithRelevance,
            'meta' => [
                'current_page' => $candidates->currentPage(),
                'last_page' => $candidates->lastPage(),
                'per_page' => $candidates->perPage(),
                'total' => $candidates->total(),
                'from' => $candidates->firstItem(),
                'to' => $candidates->lastItem(),
            ],
            'search_criteria' => [
                'skill_ids' => $skillIds,
                'total_skills_searched' => count($skillIds),
            ],
            'message' => 'Candidates retrieved successfully with skill relevance scoring.'
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
     * Get skill match statistics
     * Returns breakdown of how many candidates match different skill counts
     */
    public function getSkillMatchStats(Request $request)
    {
        $request->validate([
            'skill_ids' => 'required|array|min:1',
            'skill_ids.*' => 'integer|exists:skills,id',
        ]);

        $skillIds = $request->input('skill_ids');

        // Base query
        $baseQuery = CandidateInfo::query()
            ->whereHas('user')
            ->whereHas('skills', function($q) use ($skillIds) {
                $q->whereIn('skills.id', $skillIds);
            });

        // Exclude logged-in user if authenticated
        if ($user = $request->user('sanctum')) {
            $baseQuery->where('user_id', '!=', $user->id);
        }

        // Get candidates with matching skill counts
        $candidates = $baseQuery
            ->addSelect([
                'candidate_infos.*',
                'matching_skills_count' => DB::table('candidate_skill')
                    ->selectRaw('COUNT(*)')
                    ->whereColumn('candidate_skill.candidate_info_id', 'candidate_infos.id')
                    ->whereIn('candidate_skill.skill_id', $skillIds)
            ])
            ->get();

        // Group by matching skills count
        $statistics = $candidates->groupBy('matching_skills_count')
            ->map(function ($group, $count) use ($skillIds) {
                $percentage = count($skillIds) > 0
                    ? round(($count / count($skillIds)) * 100, 2)
                    : 0;

                return [
                    'matching_skills_count' => (int) $count,
                    'candidates_count' => $group->count(),
                    'percentage_of_searched_skills' => $percentage,
                    'label' => $this->getRelevanceLabel((int) $count, count($skillIds)),
                ];
            })
            ->sortByDesc('matching_skills_count')
            ->values();

        $totalCandidates = $candidates->count();
        $totalSkillsSearched = count($skillIds);

        return response()->json([
            'success' => true,
            'data' => [
                'statistics' => $statistics,
                'summary' => [
                    'total_candidates_found' => $totalCandidates,
                    'total_skills_searched' => $totalSkillsSearched,
                    'perfect_matches' => $statistics->where('matching_skills_count', $totalSkillsSearched)->sum('candidates_count'),
                    'partial_matches' => $statistics->where('matching_skills_count', '<', $totalSkillsSearched)->sum('candidates_count'),
                ],
            ],
            'message' => 'Skill match statistics retrieved successfully.'
        ]);
    }
}

