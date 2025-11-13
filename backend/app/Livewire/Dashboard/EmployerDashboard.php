<?php

namespace App\Livewire\Dashboard;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Jobs\Models\JobPost;
use Carbon\Carbon;
use Livewire\Component;

class EmployerDashboard extends Component
{
    public $myJobsCount = 0;
    public $activeApplications = 0;
    public $hiredCandidates = 0;
    public $pendingReviews = 0;

    public $monthlyLabels = [];
    public $monthlyData = [];

    public $recentApplications = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $employer = auth()->user();
        $jobsQuery = JobPost::where('employer_id', $employer->id);

        $this->myJobsCount = $jobsQuery->count();
        $this->activeApplications = JobApplication::whereIn('job_post_id', $jobsQuery->pluck('id'))->where('status','pending')->count();
        $this->hiredCandidates = JobApplication::whereIn('job_post_id', $jobsQuery->pluck('id'))->where('status','hired')->count();
        $this->pendingReviews = 0; // adapt to your reviews table

        // monthly applications for employer (last 6 months)
        $labels = []; $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $m = Carbon::now()->subMonths($i);
            $labels[] = $m->format('M Y');
            $data[] = JobApplication::whereYear('created_at', $m->year)
                ->whereMonth('created_at', $m->month)
                ->whereIn('job_post_id', $jobsQuery->pluck('id'))
                ->count();
        }
        $this->monthlyLabels = $labels;
        $this->monthlyData = $data;

        $this->recentApplications = JobApplication::whereIn('job_post_id', $jobsQuery->pluck('id'))
            ->latest()->take(8)->get()->map(function($app){
                return [
                    'applicant' => $app->applicant->name ?? $app->name ?? '—',
                    'job' => $app->jobPost->title ?? '—',
                    'status' => $app->status,
                ];
            })->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard.employer-dashboard')->layout('layouts.app');
    }
}
