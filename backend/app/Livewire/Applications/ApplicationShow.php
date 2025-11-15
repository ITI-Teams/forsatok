<?php

namespace App\Livewire\Applications;

use App\Domains\Applications\Models\JobApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class ApplicationShow extends Component
{
    public $application;
    public $applicationId;

    public function mount($id)
    {
        $this->applicationId = $id;
        $this->loadApplication();
    }

    public function loadApplication()
    {
        $query = JobApplication::with(['candidate', 'jobPost.employer']);

        $user = Auth::user();
        if ($user->hasRole('employer') && !$user->hasRole('admin')) {
            $query->whereHas('jobPost', function($q) use ($user) {
                $q->where('employer_id', $user->id);
            });
        }

        $this->application = $query->findOrFail($this->applicationId);
    }

    public function accept()
    {
        // التحقق من الصلاحية
        if (!$this->checkAuthorization()) {
            return;
        }

        $this->application->update(['status' => 'accepted']);
        session()->flash('message', 'Application accepted successfully.');
        $this->dispatch('applicationUpdated');
    }

    public function reject()
    {
        // التحقق من الصلاحية
        if (!$this->checkAuthorization()) {
            return;
        }

        $this->application->update(['status' => 'rejected']);
        session()->flash('message', 'Application rejected successfully.');
        $this->dispatch('applicationUpdated');
    }

    private function checkAuthorization()
    {
        $user = Auth::user();
        if ($user->hasRole('employer') && !$user->hasRole('admin')) {
            if ($this->application->jobPost->employer_id !== $user->id) {
                session()->flash('error', 'You are not authorized to perform this action.');
                return false;
            }
        }
        return true;
    }

    public function render()
    {
        return view('livewire.applications.application-show')->layout('layouts.app');
    }
}
