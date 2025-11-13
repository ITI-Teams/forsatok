<?php

namespace App\Livewire\Dashboard;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Users\Models\User;
use Livewire\Component;

class AdminDashboard extends Component
{
    public $totalUsers;
    public $totalJobs;
    public $activeEmployers;
    public $pendingJobs;
    public $totalApplications;
    public $approvedJobs;
    public $featuredJobs;
    public $totalCompanies;
    public $rejectedJobs;
    public $totalReviews;

    public $monthlyLabels = [];
    public $monthlyData = [];
    public $userRegistrations = [];
    public $latestUsers = [];
    public $recentActivities = [];

    public function mount()
    {
        $this->loadStatistics();
        $this->loadChartsData();
        $this->loadRecentActivities();
    }

    private function loadStatistics()
    {
        $this->totalUsers = User::count();
        $this->totalJobs = JobPost::count();
        $this->activeEmployers = User::where('type', 'employer')->count();
        $this->pendingJobs = JobPost::where('is_active', 'pending')->count();
        $this->totalApplications = JobApplication::count();
        $this->approvedJobs = JobPost::where('is_active', 'approved')->count();
        $this->featuredJobs = JobPost::count();
        $this->totalCompanies = User::count();
        $this->rejectedJobs = JobPost::where('is_active', 'rejected')->count();
        $this->totalReviews = 0; // Add your reviews model count here
    }

    private function loadChartsData()
    {
        // Load last 6 months data for charts
        $this->monthlyLabels = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun'];
        $this->monthlyData = [65, 59, 80, 81, 56, 55];
        $this->userRegistrations = [45, 52, 65, 70, 63, 68];

        // Load latest users
        $this->latestUsers = User::latest()->take(5)->get()->map(function($user) {
            return [
                'id' => $user->id,
                'name' => $user->name,
                'email' => $user->email,
                'role' => $user->roles->first()->name ?? 'user',
                'status' => 'active',
                'joined' => $user->created_at->format('M d, Y')
            ];
        })->toArray();
    }

    private function loadRecentActivities()
    {
        $this->recentActivities = [
            [
                'icon' => 'user-plus',
                'color' => 'success',
                'description' => 'New user registered: John Doe',
                'time' => '2 minutes ago'
            ],
            [
                'icon' => 'briefcase',
                'color' => 'primary',
                'description' => 'New job posted: Senior Developer',
                'time' => '1 hour ago'
            ],
            [
                'icon' => 'check-circle',
                'color' => 'info',
                'description' => 'Job application approved',
                'time' => '3 hours ago'
            ],
            [
                'icon' => 'exclamation-triangle',
                'color' => 'warning',
                'description' => 'Pending action required: 5 jobs need review',
                'time' => '5 hours ago'
            ]
        ];
    }

    public function changeTimeRange($range)
    {
        // Implement time range change logic
        // This will update the charts data based on selected range
        $this->loadChartsData(); // Reload data for demo
    }

    public function render()
    {
        return view('livewire.dashboard.admin-dashboard')->layout('layouts.app');
    }
}
