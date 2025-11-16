<?php

namespace App\Domains\Applications\Controllers\Api;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Applications\Requests\Api\ApplayApplicationRequest;
use App\Domains\Applications\Resources\ApplicationResource;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Jobs\Resources\JobPostResource;
use App\Domains\Users\Models\User;
use App\Events\JobApplied;
use App\Http\Controllers\Controller;
use App\Domains\Jobs\Models\SavedJob;
use App\Domains\Jobs\Requests\Api\SaveJobRequest;
use App\Notifications\JobApplicationReceived;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\Storage;

class ApplicationController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $applications = JobApplication::with([
            'candidate',
            'jobPost.employer',
            'jobPost.category'
        ])
            ->where('candidate_id', $user->id)
            ->latest()
            ->paginate($request->input('per_page', 10));

        return response()->json([
            'success' => true,
            'data' => ApplicationResource::collection($applications),
            'meta' => [
                'current_page' => $applications->currentPage(),
                'last_page' => $applications->lastPage(),
                'per_page' => $applications->perPage(),
                'total' => $applications->total(),
                'from' => $applications->firstItem(),
                'to' => $applications->lastItem(),
            ],
            'message' => 'Applications retrieved successfully.'
        ]);
    }

    public function show($id)
    {
        $user = Auth::user();

        $application = JobApplication::with(['candidate', 'jobPost.employer', 'jobPost.category'])
            ->where('candidate_id', $user->id)
            ->find($id);

        if (!$application) {
            return response()->json([
                'success' => false,
                'message' => 'Application not found or you do not have permission to view it.'
            ], 404);
        }

        return response()->json([
            'success' => true,
            'data' => new ApplicationResource($application),
            'message' => 'Application retrieved successfully.'
        ]);
    }

    public function store(ApplayApplicationRequest $request)
    {
        $user = Auth::user();

        if (!$user->hasRole('candidate')) {
            return response()->json([
                'success' => false,
                'message' => 'Only candidates can submit applications.'
            ], 403);
        }

        $validated = $request->validatedPayload();

        $existingApplication = JobApplication::where('candidate_id', $user->id)
            ->where('job_post_id', $validated['job_post_id'])
            ->first();

        if ($existingApplication) {
            return response()->json([
                'success' => false,
                'message' => 'You have already applied to this job.'
            ], 422);
        }

        $resumePath = null;
        if ($request->hasFile('resume')) {
            $resumePath = $request->file('resume')->store('resumes', 'public');
        }

        $application = JobApplication::create([
            'candidate_id' => $user->id,
            'job_post_id' => $validated['job_post_id'],
            'cover_letter' => $validated['cover_letter'] ?? null,
            'resume_path' => $resumePath,
            'status' => 'pending',
        ]);

        $application->load(['candidate', 'jobPost.employer', 'jobPost.category']);


        event(new JobApplied($application));
        // Notify employer
        $employer = User::where('id', $application->jobPost->employer_id)->get();
        Notification::send($employer, new JobApplicationReceived($application));

        return response()->json([
            'success' => true,
            'data' => new ApplicationResource($application),
            'message' => 'Application submitted successfully!'
        ], 201);
    }

    public function stats()
    {
        $user = Auth::user();

        $stats = [
            'total' => JobApplication::where('candidate_id', $user->id)->count(),
            'pending' => JobApplication::where('candidate_id', $user->id)->where('status', 'pending')->count(),
            'accepted' => JobApplication::where('candidate_id', $user->id)->where('status', 'accepted')->count(),
            'rejected' => JobApplication::where('candidate_id', $user->id)->where('status', 'rejected')->count(),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Application statistics retrieved successfully.'
        ]);
    }

    public function availableJobs()
    {
        $user = Auth::user();

        $appliedJobIds = JobApplication::where('candidate_id', $user->id)
            ->pluck('job_post_id')
            ->toArray();

        $availableJobs = JobPost::with(['employer', 'category'])
            ->whereNotIn('id', $appliedJobIds)
            ->where('is_active', 1)
            ->where('deadline', '>=', now())
            ->latest()
            ->get();

        return response()->json([
            'success' => true,
            'data' => JobPostResource::collection($availableJobs),
            'message' => 'Available jobs retrieved successfully.'
        ]);
    }
}
