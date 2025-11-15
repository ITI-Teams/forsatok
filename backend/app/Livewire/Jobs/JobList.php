<?php

namespace App\Livewire\Jobs;

use App\Domains\Jobs\Actions\Job\SoftDeleteJobAction;
use App\Domains\Jobs\Models\JobPost;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class JobList extends Component
{
    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $searchFields = ['title', 'experience'];
    public $showApprovalModal = false;
    public $selectedJob = null;

    public $selectedJobId = null;

    #[On('jobSearchUpdated')]
    public function handleSearch($payload)
    {
        $this->search = $payload['query'] ?? '';
        $this->searchFields = $payload['fields'] ?? [];
        $this->resetPage();
    }

    public function delete($id, SoftDeleteJobAction $delete)
    {
        $job = JobPost::findOrFail($id);
        $delete->execute($job);

        session()->flash('message', 'Job moved to trash!');
    }
    public function openApprovalModal($jobId)
    {
        $this->selectedJob = JobPost::findOrFail($jobId);
        $this->showApprovalModal = true;
    }

    public function closeApprovalModal()
    {
        $this->selectedJob = null;
        $this->showApprovalModal = false;
    }

    public function approveJob($jobId)
    {
        $job = JobPost::findOrFail($jobId);
        $job->update(['is_active' => 1]);

        $this->dispatch('toast', [
            'type' => 'success',
            'message' => 'Job approved successfully!'
        ]);

        $this->closeApprovalModal();
    }

    public function rejectJob($jobId)
    {
        $job = JobPost::findOrFail($jobId);
        $job->update(['is_active' => 0]);

        $this->dispatch('toast', [
            'type' => 'error',
            'message' => 'Job rejected!'
        ]);


        $this->closeApprovalModal();
    }

    public function render()
    {
        $user = auth()->user();

        $query = JobPost::with(['category', 'employer', 'location.country', 'location.city'])->latest();

        if ($user->type === 'employer') {
            $query->where('employer_id', $user->id);
        }

        if ($this->search && count($this->searchFields) > 0) {
            $query->where(function ($q) {
                foreach ($this->searchFields as $i => $field) {
                    $method = $i === 0 ? 'where' : 'orWhere';
                    $q->$method($field, 'like', "%{$this->search}%");
                }
            });
        }

        $jobs = $query->paginate(10);

        return view('livewire.jobs.job-list', compact('jobs'))->layout('layouts.app');
    }
}
