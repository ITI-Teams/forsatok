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
            ->where('status', 'hired')
            ->count();
        $this->pendingReviews = CompanyReview::where('company_id', $employerId)
            ->where('status', 'pending')
            ->count();
        $this->totalViews = $jobsQuery->sum('views') ?? 0;

        // Calculate response rate
        $totalApplications = JobApplication::whereIn('job_post_id', $jobIds)->count();
        $respondedApplications = JobApplication::whereIn('job_post_id', $jobIds)
            ->whereIn('status', ['reviewed', 'interview', 'hired', 'rejected'])
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
            $m = Carbon::now()->subMonths($i);
            $labels[] = $m->format('M');
            $data[] = JobApplication::whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->whereIn('job_post_id', $jobIds)
                ->count();
        }
        $this->monthlyLabels = $labels;
        $this->monthlyData = $data;

        // Application status distribution
        $this->applicationStatusData = [
            'pending' => JobApplication::whereIn('job_post_id', $jobIds)->where('status', 'pending')->count(),
            'reviewed' => JobApplication::whereIn('job_post_id', $jobIds)->where('status', 'reviewed')->count(),
            'interview' => JobApplication::whereIn('job_post_id', $jobIds)->where('status', 'interview')->count(),
            'hired' => JobApplication::whereIn('job_post_id', $jobIds)->where('status', 'hired')->count(),
            'rejected' => JobApplication::whereIn('job_post_id', $jobIds)->where('status', 'rejected')->count(),
        ];
    }

    public function loadRecentActivities()
    {
        $employer = auth()->user();
        $jobIds = JobPost::where('employer_id', $employer->id)->pluck('id');

        // Get real recent activities from applications
        $recentApps = JobApplication::whereIn('job_post_id', $jobIds)
            ->with('jobPost')
            ->latest()
            ->take(4)
            ->get();

        $activities = [];

        foreach ($recentApps as $application) {
            $activities[] = [
                'icon' => 'user-plus',
                'color' => 'success',
                'description' => "New application for {$application->jobPost->title}",
                'time' => $application->created_at->diffForHumans()
            ];
        }

        // If no recent applications, show default activities
        if (empty($activities)) {
            $activities = [
                [
                    'icon' => 'briefcase',
                    'color' => 'primary',
                    'description' => 'Welcome to your employer dashboard!',
                    'time' => 'Just now'
                ],
                [
                    'icon' => 'plus',
                    'color' => 'info',
                    'description' => 'Start by posting your first job',
                    'time' => '1 minute ago'
                ]
            ];
        }

        $this->recentActivities = $activities;
    }

    public function loadTopPerformingJobs()
    {
        $employer = auth()->user();

        $jobs = JobPost::where('employer_id', $employer->id)
            ->withCount('applications')
            ->orderBy('applications_count', 'desc')
            ->take(5)
            ->get();

        $this->topPerformingJobs = $jobs->map(function($job) {
            return [
                'title' => $job->title,
                'applications' => $job->applications_count,
                'views' => $job->views ?? 0,
                'status' => $job->status,
                'status_color' => $this->getStatusColor($job->status)
            ];
        })->toArray();
    }

    private function getStatusColor($status)
    {
        return match($status) {
            'active' => 'success',
            'pending' => 'warning',
            'expired' => 'secondary',
            'draft' => 'info',
            default => 'secondary'
        };
    }

    public function getApplicationStatusColor($status)
    {
        return match($status) {
            'pending' => 'warning',
            'reviewed' => 'info',
            'interview' => 'primary',
            'hired' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    public function changeTimeRange($range)
    {
        // Implement time range change logic if needed
        $this->loadChartsData();
    }

    public function render()
    {
        return view('livewire.dashboard.employer-dashboard')->layout('layouts.app');
    }
}
