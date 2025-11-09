<?php

namespace App\Livewire\Jobs;

use App\Domains\Jobs\Models\JobPost;
use Livewire\Component;

class JobShow extends Component
{
    public $job;

    public function mount($id)
    {
        $this->job = JobPost::with(['employer', 'category', 'location.country', 'location.city'])->findOrFail($id);
    }

    public function render()
    {
        return view('livewire.jobs.job-show')->layout('layouts.app');
    }
}
