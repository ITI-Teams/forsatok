<?php

namespace App\Livewire\Dashboard;

use App\Domains\Jobs\Models\JobPost;
use App\Domains\Users\Models\User;
use Carbon\Carbon;
use Livewire\Component;

class AdminDashboard extends Component
{
    public $totalUsers = 0;
    public $totalJobs = 0;
    public $activeEmployers = 0;
    public $pendingJobs = 0;

    public $monthlyLabels = [];
    public $monthlyData = [];

    public $latestUsers = [];

    public function mount()
    {
        $this->loadStats();
    }

    public function loadStats()
    {
        $this->totalUsers = User::count();
        $this->totalJobs = JobPost::count();
        $this->activeEmployers = User::where('type', 'employer')->where('is_active', true)->count();
        $this->pendingJobs = JobPost::where('is_active', false)->count();

        // monthly job postings (last 6 months)
        $labels = [];
        $data = [];
        for ($i = 5; $i >= 0; $i--) {
            $month = Carbon::now()->subMonths($i);
            $labels[] = $month->format('M Y');
            $count = JobPost::whereYear('created_at', $month->year)
                ->whereMonth('created_at', $month->month)
                ->count();
            $data[] = $count;
        }
        $this->monthlyLabels = $labels;
        $this->monthlyData = $data;

        $this->latestUsers = User::latest()->take(8)->get(['id','name','email'])->map(function($u){
            return [
                'id'=>$u->id,
                'name'=>$u->name,
                'email'=>$u->email,
                'role' => $u->getRoleNames()->first() ?? '—',
                'joined' => $u->created_at->format('Y-m-d'),
            ];
        })->toArray();
    }

    public function render()
    {
        return view('livewire.dashboard.admin-dashboard')->layout('layouts.app');
    }
}
