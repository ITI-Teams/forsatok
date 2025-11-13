<?php

namespace App\Livewire\Applications;

use App\Domains\Applications\Actions\application\SoftDeleteApplicationAction;
use App\Domains\Applications\Models\JobApplication;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class ApplicationList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $searchFields = [];

    #[On('applicationSearchUpdated')]
    public function handleSearch($payload)
    {
        $this->search = $payload['query'] ?? '';
        $this->searchFields = $payload['fields'] ?? [];
        $this->resetPage();
    }

    public function delete($id, SoftDeleteApplicationAction $delete)
    {
        $application = JobApplication::findOrFail($id);
        $user = Auth::user();

        if ($user->hasRole('employer') && !$user->hasRole('admin')) {
            if ($application->jobPost->employer_id !== $user->id) {
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
        $query = JobApplication::with(['candidate', 'jobPost.employer'])->latest();

        if ($user->hasRole('employer') && !$user->hasRole('admin')) {
            $query->whereHas('jobPost', function ($q) use ($user) {
                $q->where('employer_id', $user->id);
            });
        }

        if ($this->search && count($this->searchFields) > 0) {
            $query->where(function ($q) {
                foreach ($this->searchFields as $i => $field) {
                    if (str_contains($field, '.')) {
                        [$relation, $col] = explode('.', $field);

                        if ($i === 0) {
                            $q->whereHas($relation, fn($q2) => $q2->where($col, 'like', "%{$this->search}%"));
                        } else {
                            $q->orWhereHas($relation, fn($q2) => $q2->where($col, 'like', "%{$this->search}%"));
                        }

                    } else {
                        if ($i === 0) {
                            $q->where($field, 'like', "%{$this->search}%");
                        } else {
                            $q->orWhere($field, 'like', "%{$this->search}%");
                        }
                    }
                }
            });
        }


        $applications = $query->paginate(10);

        // Debug
        // dump($applications);
        // dump($this->search, $this->searchFields);

        return view('livewire.applications.applications-list', [
            'applications' => $applications,
        ])->layout('layouts.app');

    }
}
