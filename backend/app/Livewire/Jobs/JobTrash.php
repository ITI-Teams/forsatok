<?php

namespace App\Livewire\Jobs;

use App\Domains\Jobs\Actions\job\DeleteJobAction;
use App\Domains\Jobs\Actions\job\RestoreJobAction;
use App\Domains\Jobs\Models\JobPost;
use Livewire\Component;

class JobTrash extends Component
{
    public $trashedJobs;

    public function mount()
    {
        $this->loadTrashed();
    }
    public function loadTrashed()
    {
        $this->trashedJobs = JobPost::onlyTrashed()->latest()->get();
    }
    public function restore($id, RestoreJobAction $restore)
    {
        $restore->execute($id);
        session()->flash('message', 'Job restored successfully!');
        $this->loadTrashed();
    }

    public function forceDelete($id, DeleteJobAction $delete)
    {
        $delete->execute($id);
        session()->flash('message', 'Job permanently deleted!');
        $this->loadTrashed();
    }

    public function render()
    {
        return view('livewire.jobs.job-trash')->layout('layouts.app');
    }
}
