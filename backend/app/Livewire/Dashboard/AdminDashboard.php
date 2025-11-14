<?php

namespace App\Livewire\Dashboard;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Contact\Models\ContactMessage;
use App\Domains\Jobs\Models\Category;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Shared\Models\AuditLog;
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
    public $totalMessages;

    public $monthlyLabels = [];
    public $monthlyData = [];
    public $userRegistrations = [];
    public $latestUsers = [];
    public $recentActivities = [];
    //  Application Status
    public $applicationStatusLabels = [];
    public $applicationStatusData = [];
    public $applicationStatusColors = [];
    //  Job Categories
    public $jobCategoriesLabels = [];
    public $jobCategoriesData = [];
    public $jobCategoriesColors = [];
    public $jobCategoriesBorderColors= [];

    public $usersByLocation = [];

    public function mount()
    {
        $this->loadStatistics();
        $this->loadChartsData();
        $this->loadRecentActivities();
        $this->loadApplicationStatus();
        $this->loadJobCategories();
        $this->loadUsersByLocation();

    }

    private function loadStatistics()
    {
        $this->totalUsers = User::count();
        $this->totalJobs = JobPost::count();
        $this->activeEmployers = User::where('type', 'employer')->count();
        $this->pendingJobs = JobPost::where('is_active', 0)->count();
        $this->totalApplications = JobApplication::count();
        $this->approvedJobs = JobPost::where('is_active', 1)->count();
        $this->featuredJobs = JobPost::count();
        $this->totalCompanies = User::where('type', 'employer')->count();
        $this->rejectedJobs = JobPost::where('is_active', 0)->count();
        $this->totalMessages = ContactMessage::where('user_id', null)->count();
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
        $recentAuditLogs = AuditLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        $this->recentActivities = $recentAuditLogs->map(function ($log) {
            return $this->formatAuditLog($log);
        })->toArray();
    }

    public function changeTimeRange($range)
    {
        // Implement time range change logic
        // This will update the charts data based on selected range
        $this->loadChartsData(); // Reload data for demo
    }

    private function formatAuditLog(AuditLog $log)
    {
        $userName = $log->user ? $log->user->name : 'System';
        $action = $this->getActionText($log->action);
        $modelType = $this->getModelTypeText($log->model_type);

        $description = $this->generateDescription($log, $userName, $action, $modelType);
        $icon = $this->getActionIcon($log->action);
        $color = $this->getActionColor($log->action);

        return [
            'icon' => $icon,
            'color' => $color,
            'description' => $description,
            'time' => $log->created_at->diffForHumans()
        ];
    }

    private function getActionText($action)
    {
        return match($action) {
            'login' => 'logged in',
            'created' => 'registered',
            'updated' => 'updated',
            'deleted' => 'deleted',
            'approved' => 'approved',
            'rejected' => 'rejected',
            default => 'do action in'
        };
    }

    private function getModelTypeText($modelType)
    {
        if (!$modelType) return 'system';

        $models = [
            'App\\Domains\\Users\\Models\\User' => 'user',
            'App\\Domains\\Jobs\\Models\\JobPost' => 'job',
            'App\\Domains\\Applications\\Models\\JobApplication' => 'job Application',
            'App\\Domains\\Companies\\Models\\Company' => 'company',
            'App\\Domains\\Categories\\Models\\Category' => 'category',
        ];

        return $models[$modelType] ?? 'item';
    }

    private function generateDescription($log, $userName, $action, $modelType)
    {
        $baseDescription = "{$userName} {$action} {$modelType}";

        if ($log->model_id && $log->model_type) {
            $modelName = $this->getModelName($log->model_type, $log->model_id);
            if ($modelName) {
                $baseDescription .= ": {$modelName}";
            } else {
                $baseDescription .= " #{$log->model_id}";
            }
        }

        if ($log->action === 'login') {
            $baseDescription .= " From IP: {$log->ip_address}";
        }

        return $baseDescription;
    }

    private function getModelName($modelType, $modelId)
    {
        try {
            $model = $modelType::find($modelId);
            if ($model) {
                if (method_exists($model, 'getName')) {
                    return $model->getName();
                } elseif (isset($model->name)) {
                    return $model->name;
                } elseif (isset($model->title)) {
                    return $model->title;
                } elseif (isset($model->email)) {
                    return $model->email;
                }
            }
        } catch (\Exception $e) {
        }

        return null;
    }

    private function getActionIcon($action)
    {
        return match($action) {
            'login' => 'sign-in',
            'created' => 'plus-circle',
            'updated' => 'edit',
            'deleted' => 'trash',
            'approved' => 'check-circle',
            'rejected' => 'times-circle',
            default => 'history'
        };
    }

    private function getActionColor($action)
    {
        return match($action) {
            'login' => 'info',
            'created' => 'success',
            'updated' => 'primary',
            'deleted' => 'danger',
            'approved' => 'success',
            'rejected' => 'danger',
            default => 'secondary'
        };
    }

    private function loadApplicationStatus()
    {
        $statuses = ['pending', 'accepted', 'rejected'];

        $this->applicationStatusLabels = [];
        $this->applicationStatusData = [];
        $this->applicationStatusColors = [
            'rgba(255, 206, 86, 0.7)',
            'rgba(75, 192, 192, 0.7)',
            'rgba(255, 99, 132, 0.7)'
        ];
        foreach ($statuses as $status) {
            $this->applicationStatusLabels[] = ucfirst($status);
            $count = JobApplication::where('status', $status)->count();
            $this->applicationStatusData[] = $count;
        }

        if (array_sum($this->applicationStatusData) === 0) {
            $this->applicationStatusData = [5, 3, 2]; // بيانات تجريبية
        }
    }

    private function loadJobCategories()
    {
        $categories = Category::withCount('jobs')->get();

        $this->jobCategoriesLabels = $categories->pluck('name')->toArray();
        $this->jobCategoriesData = $categories->pluck('jobs_count')->toArray();

        $baseColors = [
            'rgba(59, 130, 246, 0.8)',
            'rgba(16, 185, 129, 0.8)',
            'rgba(245, 158, 11, 0.8)',
            'rgba(139, 92, 246, 0.8)',
            'rgba(14, 165, 233, 0.8)',
            'rgba(244, 63, 94, 0.8)',
            'rgba(168, 85, 247, 0.8)',
            'rgba(34, 197, 94, 0.8)',
            'rgba(234, 179, 8, 0.8)',
            'rgba(217, 70, 239, 0.8)'
        ];
        $this->jobCategoriesColors = [];
        $this->jobCategoriesBorderColors = [];

        $colorCount = count($baseColors);

        for ($i = 0; $i < count($this->jobCategoriesLabels); $i++) {
            $baseColor = $baseColors[$i % $colorCount];
            $this->jobCategoriesColors[] = $baseColor;
            $this->jobCategoriesBorderColors[] = str_replace('0.8', '1', $baseColor);
        }
    }

    private function loadUsersByLocation()
    {
        $users = User::whereHas('candidateInfo.location')
            ->with('candidateInfo.location')
            ->get();

        $locations = $users->map(function($user) {
            $country = $user->candidateInfo->location->country ?? null;
            return $country ? $country->name : null;
        })->filter()
        ->countBy()
        ->map(function($count, $country) {
            return [
                'country' => $country,
                'count' => $count
            ];
        })->values()->toArray();

        $this->usersByLocation = $locations;
    }


    public function render()
    {
        return view('livewire.dashboard.admin-dashboard')->layout('layouts.app');
    }
}
