<?php

namespace App\Livewire\Dashboard;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\CompanyReviews\Models\CompanyReview;
use Carbon\Carbon;
use Livewire\Component;
use Illuminate\Support\Collection;

class EmployerDashboard extends Component
{
    public $myJobsCount = 0;
    public $activeApplications = 0;
    public $hiredCandidates = 0;
    public $pendingReviews = 0;
    public $totalViews = 0;
    public $responseRate = 0;

    public $monthlyLabels = [];
    public $monthlyData = [];
    public $applicationStatusData = [];
    public $recentApplications = [];
    public $recentActivities = [];
    public $topPerformingJobs = [];

    public function mount()
    {
        $this->loadStats();
        $this->loadChartsData();
        $this->loadRecentApplications();
        $this->loadRecentActivities();
        $this->loadTopPerformingJobs();
    }

    public function loadStats()
    {
        $employer = auth()->user();
        $employerId = $employer->id;

        // Get employer's jobs
        $jobsQuery = JobPost::where('employer_id', $employerId);
        $jobIds = $jobsQuery->pluck('id');

        $this->myJobsCount = $jobsQuery->count();

        $this->activeApplications = JobApplication::whereIn('job_post_id', $jobIds)
            ->where('status', 'pending')
            ->count();

        $this->hiredCandidates = JobApplication::whereIn('job_post_id', $jobIds)
            ->where('status', 'accepted')
            ->count();

        $this->pendingReviews = CompanyReview::where('company_id', $employerId)
            ->where('status', 'pending')
            ->count();

        $this->totalViews = $jobsQuery->sum('views') ?? 0;

        //Calculate response rate
        $totalApplications = JobApplication::whereIn('job_post_id', $jobIds)->count();
        $respondedApplications = JobApplication::whereIn('job_post_id', $jobIds)
            ->whereIn('status', ['accepted', 'rejected', 'pending'])
            ->count();

        $this->responseRate = $totalApplications > 0
            ? round(($respondedApplications / $totalApplications) * 100, 1)
            : 0;
    }

    public function loadChartsData()
    {
        $employer = auth()->user();
        $jobsQuery = JobPost::where('employer_id', $employer->id);
        $jobIds = $jobsQuery->pluck('id');

        // Monthly applications for employer (last 6 months)
        $labels = [];
        $data = [];

        for ($i = 5; $i >= 0; $i--) {
            $startOfMonth = Carbon::now()->subMonths($i)->startOfMonth();
            $endOfMonth = Carbon::now()->subMonths($i)->endOfMonth();

            $labels[] = $startOfMonth->format('M Y');

            $monthlyCount = JobApplication::whereIn('job_post_id', $jobIds)
                ->whereBetween('created_at', [$startOfMonth, $endOfMonth])
                ->count();

            $data[] = $monthlyCount;
        }

        $this->monthlyLabels = $labels;
        $this->monthlyData = $data;

        // Application status distribution -
        $this->applicationStatusData = [
            'pending' => JobApplication::whereIn('job_post_id', $jobIds)
                ->where('status', 'pending')->count(),
            'accepted' => JobApplication::whereIn('job_post_id', $jobIds)
                ->where('status', 'accepted')->count(),
            'rejected' => JobApplication::whereIn('job_post_id', $jobIds)
                ->where('status', 'rejected')->count(),
        ];
    }

    public function loadRecentApplications()
    {
        $employer = auth()->user();
        $jobIds = JobPost::where('employer_id', $employer->id)->pluck('id');

        $applications = JobApplication::whereIn('job_post_id', $jobIds)
            ->with(['jobPost', 'candidate'])
            ->latest()
            ->take(6)
            ->get();

        $this->recentApplications = $applications->map(function($application) {
            return [
                'id' => $application->id,
                'applicant' => $application->user->name ?? 'Unknown',
                'job' => $application->jobPost->title ?? 'Deleted Job',
                'applied_date' => $application->created_at->format('M d, Y'),
                'status' => $application->status,
                'email' => $application->user->email ?? 'N/A',
            ];
        })->toArray();
    }

    public function loadRecentActivities()
    {
        $employer = auth()->user();
        $jobIds = JobPost::where('employer_id', $employer->id)->pluck('id');

        $activities = [];

        // Recent applications
        $recentApps = JobApplication::whereIn('job_post_id', $jobIds)
            ->with('jobPost')
            ->latest()
            ->take(3)
            ->get();

        foreach ($recentApps as $application) {
            $activities[] = [
                'icon' => 'user-plus',
                'color' => 'success',
                'description' => "New application for {$application->jobPost->title}",
                'time' => $application->created_at->diffForHumans()
            ];
        }

        // Recent job posts
        $recentJobs = JobPost::where('employer_id', $employer->id)
            ->latest()
            ->take(2)
            ->get();

        foreach ($recentJobs as $job) {
            $activities[] = [
                'icon' => 'briefcase',
                'color' => 'primary',
                'description' => "New job posted: {$job->title}",
                'time' => $job->created_at->diffForHumans()
            ];
        }

        // Sort activities by time
        usort($activities, function($a, $b) {
            return strtotime($b['time']) - strtotime($a['time']);
        });

        // Take only 4 most recent
        $this->recentActivities = array_slice($activities, 0, 4);

        // If no activities, show default
        if (empty($this->recentActivities)) {
            $this->recentActivities = [
                [
                    'icon' => 'briefcase',
                    'color' => 'primary',
                    'description' => 'Welcome to your employer dashboard!',
                    'time' => 'Just now'
                ]
            ];
        }
    }

    public function loadTopPerformingJobs()
    {
        $employer = auth()->user();

        $jobs = JobPost::where('employer_id', $employer->id)
            ->withCount(['applications'])
            ->take(5)
            ->get();

        $this->topPerformingJobs = $jobs->map(function($job) {
            return [
                'id' => $job->id,
                'title' => $job->title,
                'applications' => $job->applications_count,
                'views' => $job->views ?? 0,
                'is_active' => $job->is_active,
                'status_color' => $this->getStatusColor($job->is_active),
                'created_at' => $job->created_at->format('M d, Y')
            ];
        })->toArray();
    }

    private function getStatusColor($status)
    {
        return match($status) {
            1 => 'success',
            0 => 'warning',
            default => 'secondary'
        };
    }

    public function getApplicationStatusColor($status)
    {
        return match($status) {
            'pending' => 'warning',
            'accepted' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    public function changeTimeRange($range)
    {
        $this->loadChartsData();
        $this->dispatch('updateCharts', [
            'labels' => $this->monthlyLabels,
            'data' => $this->monthlyData,
            'status' => $this->applicationStatusData
        ]);
    }

    public function refreshData()
    {
        $this->loadStats();
        $this->loadChartsData();
        $this->loadRecentApplications();
        $this->loadRecentActivities();
        $this->loadTopPerformingJobs();
    }

    public function render()
    {
        return view('livewire.dashboard.employer-dashboard')->layout('layouts.app');
    }
}
