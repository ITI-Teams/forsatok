<?php

namespace App\Livewire\Jobs;

use App\Domains\Jobs\Models\JobPost;
use Livewire\Component;

use Livewire\WithPagination;

class JobShow extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $job;

    public function mount($id)
    {
        $this->job = JobPost::with(['employer', 'category', 'location.country', 'location.city'])
            ->findOrFail($id);
    }

    public function render()
    {
        $decisions = $this->job->decisions()->with('admin')->paginate(5);
        return view('livewire.jobs.job-show', compact('decisions'))->layout('layouts.app');
    }
}
