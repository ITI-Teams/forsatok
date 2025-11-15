<?php

namespace App\Livewire\Applications;

use App\Domains\Applications\Actions\Application\DeleteApplicationAction;
use App\Domains\Applications\Actions\Application\RestoreApplicationAction;
use App\Domains\Applications\Models\JobApplication;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ApplicationTrash extends Component
{
    public $trashedApplications;

    public function mount()
    {
        $this->loadTrashed();
    }

    public function loadTrashed()
    {
        $user = Auth::user();

        $query = JobApplication::onlyTrashed()
            ->with(['candidate', 'jobPost.employer'])
            ->latest();

        if ($user->hasRole('employer') && !$user->hasRole('admin')) {
            $query->whereHas('jobPost', function($q) use ($user) {
                $q->where('employer_id', $user->id);
            });
        }

        $this->trashedApplications = $query->get();
    }

    public function restore($id, RestoreApplicationAction $restore)
    {
        $application = JobApplication::onlyTrashed()->findOrFail($id);

        $user = Auth::user();
        if ($user->hasRole('employer') && !$user->hasRole('admin')) {
            if ($application->jobPost->employer_id !== $user->id) {
                session()->flash('error', 'You are not authorized to restore this application.');
                return;
            }
        }

        $restore->execute($id);
        session()->flash('message', ' Application restored successfully!');
        $this->loadTrashed();
    }

    public function forceDelete($id, DeleteApplicationAction $forceDelete)
    {
        $application = JobApplication::onlyTrashed()->findOrFail($id);

        $user = Auth::user();
        if ($user->hasRole('employer') && !$user->hasRole('admin')) {
            if ($application->jobPost->employer_id !== $user->id) {
                session()->flash('error', 'You are not authorized to delete this application.');
                return;
            }
        }

        if ($application->resume_path && Storage::disk('public')->exists($application->resume_path)) {
            Storage::disk('public')->delete($application->resume_path);
        }
        $forceDelete->execute($id);
        session()->flash('message', ' Application permanently deleted!');
        $this->loadTrashed();
    }
    public function emptyTrash()
    {
        $user = Auth::user();

        $query = JobApplication::onlyTrashed();

        if ($user->hasRole('employer') && !$user->hasRole('admin')) {
            $query->whereHas('jobPost', function($q) use ($user) {
                $q->where('employer_id', $user->id);
            });
        }

        $applications = $query->get();

        foreach ($applications as $application) {
            $resumePath = str_replace('storage/', '', $application->resume_path);

            if ($resumePath && Storage::disk('public')->exists($resumePath)) {
                Storage::disk('public')->delete($resumePath);
            }

            $application->forceDelete();
        }

        session()->flash('message', 'Trash emptied successfully! All applications have been permanently deleted.');
        $this->loadTrashed();
    }

    public function render()
    {
        return view('livewire.applications.applications-trash')->layout('layouts.app');
    }
}
