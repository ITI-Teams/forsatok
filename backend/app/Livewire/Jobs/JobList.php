<?php

namespace App\Livewire\Jobs;

use App\Domains\Jobs\Actions\Job\SoftDeleteJobAction;
use App\Domains\Jobs\Models\JobPost;
use Livewire\Component;

class JobList extends Component
{
    public $jobs;
    public $selectedJobId = null;
    public $confirmingDelete = false;

    public function mount()
    {
        $this->loadJobs();
    }

    public function loadJobs()
    {
        $this->jobs = JobPost::with(['category', 'employer'])->latest()->get();
    }


    public function confirmDelete($id)
    {
        $this->selectedJobId = $id;
        $this->confirmingDelete = true;
    }
    public function cancelDelete()
    {
        $this->selectedJobId = null;
        $this->confirmingDelete = false;
    }
    public function delete(SoftDeleteJobAction $delete)
    {
        $job = JobPost::findOrFail($this->selectedJobId);
        $delete->execute($job);

        session()->flash('message', 'Job moved to trash!');
        $this->loadJobs();

    }

    public function render()
    {
        return view('livewire.jobs.job-list')->layout('layouts.app');
    }
}
