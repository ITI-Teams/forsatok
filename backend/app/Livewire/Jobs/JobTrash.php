<?php

namespace App\Livewire\Jobs;

use App\Domains\Jobs\Actions\Job\DeleteJobAction;
use App\Domains\Jobs\Actions\Job\RestoreJobAction;
use App\Domains\Jobs\Models\JobPost;
use Livewire\Component;

class JobTrash extends Component
{
    public $trashedJobs;
    public $confirmingDelete = false;
    public $selectedJobId = null;

    public function mount()
    {
        $this->loadTrashed();
    }




    public function loadTrashed()
    {
        $this->trashedJobs = JobPost::onlyTrashed()
        ->with(['category', 'employer', 'location.country', 'location.city'])
        ->latest()
        ->get();
    }




    public function restore($id, RestoreJobAction $restore)
    {
        $restore->execute($id);
        session()->flash('message', 'Job restored successfully!');
        $this->loadTrashed();
    }

     public function confirmForceDelete($id)
    {
        $this->selectedJobId = $id;
        $this->confirmingDelete = true;
    }

    public function cancelForceDelete()
    {
        $this->selectedJobId = null;
        $this->confirmingDelete = false;
    }

    public function forceDelete(DeleteJobAction $delete)
    {
        $delete->execute($this->selectedJobId);
        session()->flash('message', 'Job permanently deleted!');
        $this->confirmingDelete = false;
        $this->selectedJobId = null;
        $this->loadTrashed();
    }

    public function render()
    {
        return view('livewire.jobs.job-trash')->layout('layouts.app');
    }
}
