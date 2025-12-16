<?php

namespace App\Domains\Applications\Controllers\Dashboard;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Applications\Resources\ApplicationResource;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class FilterController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        $user = $request->user();
        $query = JobApplication::with(['candidate.candidateInfo', 'jobPost.employer', 'jobPost.skills', 'jobPost.category'])
            ->latest();

        // Ensure employer only sees applications for their jobs
        $query->whereHas('jobPost', function ($q) use ($user) {
            $q->where('employer_id', $user->id);
        });

        $this->applyFilters($query, $request);

        $perPage = min(100, max(1, (int) $request->input('per_page', 10)));
        $applications = $query->paginate($perPage);

        return response()->json([
            'status' => true,
            'data' => ApplicationResource::collection($applications),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
            ],
        ]);
    }

    protected function applyFilters($query, Request $request)
    {
        // Status filters
        if ($status = $request->input('status')) {
            $query->where('status', $status);
        }

        if ($candidateName = $request->input('candidate_name')) {
            $query->whereHas('candidate', function ($q) use ($candidateName) {
                $q->where('name', 'like', "%{$candidateName}%");
            });
        }

        if ($candidateEmail = $request->input('candidate_email')) {
            $query->whereHas('candidate', function ($q) use ($candidateEmail) {
                $q->where('email', 'like', "%{$candidateEmail}%");
            });
        }

        // Job id filters
        if ($jobId = $request->input('job_id')) {
            $query->where('job_post_id', $jobId);
        }

        if ($jobTitle = $request->input('job_title')) {
            $query->whereHas('jobPost', function ($q) use ($jobTitle) {
                $q->where('title', 'like', "%{$jobTitle}%");
            });
        }

        if ($jobLocation = $request->input('job_location')) {
            $query->whereHas('jobPost.locationable', function ($q) use ($jobLocation) {
                $q->where('address', 'like', "%{$jobLocation}%");
            });
        }

        if ($employmentType = $request->input('employment_type')) {
            $query->whereHas('jobPost', function ($q) use ($employmentType) {
                $q->where('work_type', $employmentType);
            });
        }

        if ($workPlace = $request->input('work_place')) {
            $query->whereHas('jobPost', function ($q) use ($workPlace) {
                $q->where('work_place', $workPlace);
            });
        }

        if ($experienceLevel = $request->input('experience_level')) {
            $query->whereHas('jobPost', function ($q) use ($experienceLevel) {
                $q->where('experience', $experienceLevel);
            });
        }

        if ($salaryMin = $request->input('salary_min')) {
            $query->whereHas('jobPost', function ($q) use ($salaryMin) {
                $q->where('salary_min', '>=', $salaryMin);
            });
        }

        if ($salaryMax = $request->input('salary_max')) {
            $query->whereHas('jobPost', function ($q) use ($salaryMax) {
                $q->where('salary_max', '<=', $salaryMax);
            });
        }

        if ($deadlineFrom = $request->input('deadline_from')) {
            $query->whereHas('jobPost', function ($q) use ($deadlineFrom) {
                $q->whereDate('deadline', '>=', $deadlineFrom);
            });
        }

        if ($deadlineTo = $request->input('deadline_to')) {
            $query->whereHas('jobPost', function ($q) use ($deadlineTo) {
                $q->whereDate('deadline', '<=', $deadlineTo);
            });
        }

        if ($appliedDateFrom = $request->input('applied_date_from')) {
            $query->whereDate('created_at', '>=', $appliedDateFrom);
        }

        if ($appliedDateTo = $request->input('applied_date_to')) {
            $query->whereDate('created_at', '<=', $appliedDateTo);
        }

        if ($companyName = $request->input('company_name')) {
            $query->whereHas('jobPost.employer', function ($q) use ($companyName) {
                $q->where('company_name', 'like', "%{$companyName}%");
            });
        }

        if ($categoryName = $request->input('category_name')) {
            $query->whereHas('jobPost.category', function ($q) use ($categoryName) {
                $q->where('name', 'like', "%{$categoryName}%");
            });
        }

        if ($skillName = $request->input('skill_name')) {
            $query->whereHas('jobPost.skills', function ($q) use ($skillName) {
                $q->where('name', 'like', "%{$skillName}%");
            });
        }
    }
}
