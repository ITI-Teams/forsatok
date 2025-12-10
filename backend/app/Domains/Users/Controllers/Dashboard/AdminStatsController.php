<?php

namespace App\Domains\Users\Controllers\Dashboard;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Contact\Models\ContactMessage;
use App\Domains\Employers\Models\EmployerInfo;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Shared\Models\AuditLog;
use App\Domains\Users\Models\User;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;

/**
 * Admin Dashboard Stats Controller
 *
 * Provides statistics for the admin dashboard overview.
 */
class AdminStatsController extends Controller
{
    /**
     * Get admin dashboard statistics.
     */
    public function index(): JsonResponse
    {
        $totalUsers = User::count();
        $totalJobs = JobPost::count();
        $activeEmployers = User::role('employer')->count();
        $pendingJobs = JobPost::where('is_active', 0)->count();
        $totalApplications = JobApplication::count();
        $approvedJobs = JobPost::where('is_active', 1)->count();
        $featuredJobs = JobPost::count();
        $totalCompanies = EmployerInfo::count();
        $rejectedJobs = JobPost::where('is_active', 0)->count();
        $totalMessages = ContactMessage::count();

        $latestUsers = User::with('roles')
            ->latest()
            ->take(5)
            ->get()
            ->map(function ($user) {
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'role' => $user->roles->first()->name ?? 'user',
                    'joined' => $user->created_at->format('M d, Y'),
                ];
            });

        $recentActivities = AuditLog::with('user')
            ->latest()
            ->take(10)
            ->get()
            ->map(function (AuditLog $log) {
                return [
                    'id' => $log->id,
                    'action' => $log->action,
                    'model_type' => $log->model_type,
                    'model_id' => $log->model_id,
                    'description' => $log->changes,
                    'user' => $log->user?->only(['id', 'name']),
                    'created_at' => $log->created_at,
                ];
            });

        return response()->json([
            'status' => true,
            'data' => [
                'stats' => [
                    'total_users' => $totalUsers,
                    'total_jobs' => $totalJobs,
                    'active_employers' => $activeEmployers,
                    'pending_jobs' => $pendingJobs,
                    'total_applications' => $totalApplications,
                    'approved_jobs' => $approvedJobs,
                    'featured_jobs' => $featuredJobs,
                    'total_companies' => $totalCompanies,
                    'rejected_jobs' => $rejectedJobs,
                    'total_messages' => $totalMessages,
                ],
                'latest_users' => $latestUsers,
                'recent_activities' => $recentActivities,
            ],
        ]);
    }
}
