<?php

namespace App\Livewire\Jobs;

use App\Domains\Jobs\Actions\Job\{
    CreateJobAction,
    UpdateJobAction
};

use App\Domains\Jobs\Requests\Job\{
    StoreJobRequest,
    UpdateJobRequest
};
use App\Domains\Jobs\Models\JobPost;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class JobForm extends Component
{
    public $jobId;
    public $title;
    public $experience;
    public $description;
    public $salary_min;
    public $salary_max;
    public $type = 'full-time';
    public $location;
    public $deadline;
    public $category_id;
    public $is_active = true;

    public function mount($job = null)
    {
        if ($job) {
            $model = JobPost::findOrFail($job);
            $this->jobId = $model->id;
            $this->fill($model->toArray());
        }
    }

    public function save(CreateJobAction $create, UpdateJobAction $update)
    {
        // Choose request type
        if ($this->jobId) {
            $request = new UpdateJobRequest();
            $request->merge($this->getJobData());
            $validated = Validator::make($request->all(), $request->rules())->validate();

            $job = JobPost::findOrFail($this->jobId);
            $update->execute($job, $validated);
            session()->flash('message', '✅ Job updated successfully!');
        } else {
            $request = new StoreJobRequest();
            $request->merge($this->getJobData());
            $validated = Validator::make($request->all(), $request->rules())->validate();

            $create->execute($validated);
            session()->flash('message', '✅ Job created successfully!');
        }

        return $this->redirectRoute('jobs.index', navigate: true);
    }

    /**
     * Gather the job data for request validation
     */
    protected function getJobData(): array
    {
        return [
            'title'        => $this->title,
            'experince'   => $this->experience,
            'description'  => $this->description,
            'salary_min'   => $this->salary_min,
            'salary_max'   => $this->salary_max,
            'type'         => $this->type,
            'location'     => $this->location,
            'deadline'     => $this->deadline,
            'category_id'  => $this->category_id,
            'is_active'    => $this->is_active,
        ];
    }

    public function cancel()
    {
        return $this->redirectRoute('jobs.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.jobs.job-form')->layout('layouts.app');
    }
}
