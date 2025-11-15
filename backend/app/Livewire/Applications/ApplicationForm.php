<?php

namespace App\Livewire\Applications;

use App\Domains\Applications\Models\JobApplication;
use App\Domains\Applications\Actions\Application\CreateApplicationAction;
use App\Domains\Applications\Actions\Application\UpdateApplicationAction;
use App\Domains\Applications\Requests\StoreApplicationRequest;
use App\Domains\Applications\Requests\UpdateApplicationRequest;
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Users\Models\User;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;
use Livewire\WithFileUploads;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class ApplicationForm extends Component
{
    use WithFileUploads;

    public $applicationId, $candidate_id, $job_post_id, $cover_letter, $resume, $status;
    public $candidates = [], $jobPosts = [];
    public $currentResumePath;

    protected $rules = [
        'candidate_id' => 'required|exists:users,id',
        'job_post_id' => 'required|exists:job_posts,id',
        'cover_letter' => 'nullable|string',
        'resume' => 'nullable|file|mimes:pdf,doc,docx,txt|max:5120',
        'status' => 'required|in:pending,accepted,rejected',
    ];

    protected $messages = [
        'candidate_id.required' => 'Please select a candidate.',
        'job_post_id.required' => 'Please select a job post.',
        'job_post_id.unique_application' => 'This candidate has already applied to this job.',
    ];

    public function mount($application = null)
    {
        $user = Auth::user();
        $this->candidates = User::where('type', 'candidate')->get();

        if ($user->hasRole('employer') && !$user->hasRole('admin')) {
            $this->jobPosts = JobPost::where('employer_id', $user->id)->get();
        } else {
            $this->jobPosts = JobPost::all();
        }

        if ($application) {
            $model = JobApplication::findOrFail($application);
            $this->applicationId = $model->id;
            $this->candidate_id = $model->candidate_id;
            $this->job_post_id = $model->job_post_id;
            $this->cover_letter = $model->cover_letter;
            $this->currentResumePath = $model->resume_path;
            $this->status = $model->status;
        }
    }

    public function getRules()
    {
        $rules = $this->rules;
        if (!$this->applicationId) {
            $rules['job_post_id'] = [
                'required',
                'exists:job_posts,id',
                function ($attribute, $value, $fail) {
                    $existingApplication = JobApplication::where('candidate_id', $this->candidate_id)
                        ->where('job_post_id', $value)
                        ->first();

                    if ($existingApplication) {
                        $candidateName = $this->candidates->firstWhere('id', $this->candidate_id)?->name ?? 'The candidate';
                        $jobTitle = $this->jobPosts->firstWhere('id', $value)?->title ?? 'this job';
                        $fail("{$candidateName} has already applied to {$jobTitle}.");
                    }
                }
            ];
        }

        return $rules;
    }

    public function updated($propertyName)
    {
        if (in_array($propertyName, ['candidate_id', 'job_post_id']) && $this->candidate_id && $this->job_post_id) {
            $this->validateOnly('job_post_id');
        }

        $this->validateOnly($propertyName);
    }
    public function save(CreateApplicationAction $create, UpdateApplicationAction $update)
    {
        $this->validate($this->getRules());

        $resumePath = $this->currentResumePath;
        if ($this->resume) {
            if ($this->currentResumePath && Storage::exists($this->currentResumePath)) {
                Storage::delete($this->currentResumePath);
            }
            $resumePath = $this->resume->store('resumes', 'public');
        }

        if ($this->applicationId) {
            $request = new UpdateApplicationRequest();
            $request->merge([
                'candidate_id' => $this->candidate_id,
                'job_post_id' => $this->job_post_id,
                'cover_letter' => $this->cover_letter,
                'resume_path' => $resumePath,
                'status' => $this->status,
                'application_id' => $this->applicationId,
            ]);
            $validated = Validator::make($request->all(), $request->rules())->validate();

            $application = JobApplication::findOrFail($this->applicationId);
            $update->execute($application, $validated);
            session()->flash('message', '✅ Application updated successfully!');
        } else {
            $existingApplication = JobApplication::where('candidate_id', $this->candidate_id)
                ->where('job_post_id', $this->job_post_id)
                ->first();

            if ($existingApplication) {
                $this->addError('job_post_id', 'This candidate has already applied to this job.');
                return;
            }
            $request = new StoreApplicationRequest();
            $request->merge([
                'candidate_id' => $this->candidate_id,
                'job_post_id' => $this->job_post_id,
                'cover_letter' => $this->cover_letter,
                'resume_path' => $resumePath,
                'status' => $this->status,
            ]);
            $validated = Validator::make($request->all(), $request->rules())->validate();

            $create->execute($validated);
            session()->flash('message', '✅ Application created successfully!');
        }

        return $this->redirectRoute('job.app.index', navigate: true);
    }

    public function cancel()
    {
        return $this->redirectRoute('job.app.index', navigate: true);
    }



    public function render()
    {
        return view('livewire.applications.applications-form')->layout('layouts.app');
    }
}
