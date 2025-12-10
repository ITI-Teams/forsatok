<?php

namespace App\Domains\Employers\Controllers\Dashboard;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\CompanyReviews\Models\CompanyReview;
use App\Domains\Jobs\Models\JobPost;
use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

/**
 * Employer Dashboard Stats Controller
 *
 * Provides statistics for the employer dashboard overview.
 */
class EmployerStatsController extends Controller
{
    /**
     * Get employer dashboard statistics.
     */
    public function index(Request $request): JsonResponse
    {
        $user = $request->user();
        $jobIds = JobPost::where('employer_id', $user->id)->pluck('id');

        $myJobsCount = $jobIds->count();
        $activeApplications = JobApplication::whereIn('job_post_id', $jobIds)
            ->where('status', 'pending')
            ->count();
        $hiredCandidates = JobApplication::whereIn('job_post_id', $jobIds)
            ->where('status', 'accepted')
            ->count();
        $pendingReviews = CompanyReview::where('company_id', $user->id)
            ->where('status', 'pending')
            ->count();
        $totalViews = JobPost::where('employer_id', $user->id)->sum('views') ?? 0;

        $totalApplications = JobApplication::whereIn('job_post_id', $jobIds)->count();
        $respondedApplications = JobApplication::whereIn('job_post_id', $jobIds)
            ->whereIn('status', ['pending', 'accepted', 'rejected'])
            ->count();
        $responseRate = $totalApplications > 0
            ? round(($respondedApplications / $totalApplications) * 100, 1)
            : 0;

        $monthlyLabels = [];
        $monthlyData = [];
        for ($i = 5; $i >= 0; $i--) {
            $startOfMonth = Carbon::now()->subMonths($i)->startOfMonth();
            $endOfMonth = Carbon::now()->subMonths($i)->endOfMonth();
            $monthlyLabels[] = $startOfMonth->format('M Y');
            $monthlyData[] = JobApplication::whereIn('job_post_id', $jobIds)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();
        }

        $recentApplications = JobApplication::whereIn('job_post_id', $jobIds)
            ->with(['jobPost', 'candidate'])
            ->latest()
            ->take(6)
            ->get()
            ->map(function (JobApplication $application) {
                return [
                    'id' => $application->id,
                    'candidate' => $application->candidate?->only(['id', 'name', 'email']),
                    'job' => $application->jobPost?->only(['id', 'title']),
                    'status' => $application->status,
                    'applied_at' => $application->created_at,
                ];
            });

        $recentActivities = [];
        $recentApps = JobApplication::whereIn('job_post_id', $jobIds)
            ->with('jobPost')
            ->latest()
            ->take(3)
            ->get();
        foreach ($recentApps as $application) {
            $recentActivities[] = [
                'type' => 'application',
                'description' => 'New application for ' . ($application->jobPost->title ?? 'job'),
                'time' => $application->created_at->diffForHumans(),
            ];
        }

        $recentJobs = JobPost::where('employer_id', $user->id)
            ->latest()
            ->take(2)
            ->get();
        foreach ($recentJobs as $job) {
            $recentActivities[] = [
                'type' => 'job',
                'description' => 'New job posted: ' . $job->title,
                'time' => $job->created_at->diffForHumans(),
            ];
        }

        $topJobs = JobPost::where('employer_id', $user->id)
            ->withCount('applications')
            ->orderByDesc('applications_count')
            ->take(5)
            ->get();

        return response()->json([
            'status' => true,
            'data' => [
                'stats' => [
                    'my_jobs' => $myJobsCount,
                    'active_applications' => $activeApplications,
                    'hired_candidates' => $hiredCandidates,
                    'pending_reviews' => $pendingReviews,
                    'total_views' => $totalViews,
                    'response_rate' => $responseRate,
                ],
                'charts' => [
                    'monthly_labels' => $monthlyLabels,
                    'monthly_data' => $monthlyData,
                    'status_distribution' => [
                        'pending' => JobApplication::whereIn('job_post_id', $jobIds)->where('status', 'pending')->count(),
                        'accepted' => JobApplication::whereIn('job_post_id', $jobIds)->where('status', 'accepted')->count(),
                        'rejected' => JobApplication::whereIn('job_post_id', $jobIds)->where('status', 'rejected')->count(),
                    ],
                ],
                'recent_applications' => $recentApplications,
                'recent_activities' => array_slice($recentActivities, 0, 4),
                'top_jobs' => $topJobs,
            ],
        ]);
    }
}
