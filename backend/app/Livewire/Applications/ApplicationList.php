<?php

namespace App\Livewire\Applications;

use App\Domains\Applications\Actions\application\SoftDeleteApplicationAction;
use App\Domains\Applications\Models\JobApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;

class ApplicationList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';

    public function updatingSearch()
    {
        $this->resetPage();
    }

    public function delete($id, SoftDeleteApplicationAction $delete)
    {
        $application = JobApplication::findOrFail($id);
        $user = Auth::user();

        if ($user->hasRole('employer') && !$user->hasRole('admin')) {
            if ($application->jobPost->user_id !== $user->id) {
                session()->flash('error', 'You are not authorized to delete this application.');
                return;
            }
        }

        $delete->execute($application);
        session()->flash('message', 'Application moved to trash!');
    }

    public function render()
    {
        $user = Auth::user();

        $query = JobApplication::with(['candidate', 'jobPost.employer'])
            ->latest();

        if ($user->hasRole('employer') && !$user->hasRole('admin')) {
            $query->whereHas('jobPost', function ($q) use ($user) {
                $q->where('user_id', $user->id);
            });
        }

        if ($this->search) {
            $query->whereHas('candidate', function ($q) {
                $q->where('name', 'like', '%' . $this->search . '%');
            });
        }

        return view('livewire.applications.applications-list', [
            'applications' => $query->paginate(10),
        ])->layout('layouts.app');
    }
}
