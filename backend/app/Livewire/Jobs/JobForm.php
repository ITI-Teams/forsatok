<?php

namespace App\Livewire\Jobs;

use App\Domains\Users\Models\User;
use App\Events\JobCreated;
use App\Notifications\JobCreatedNotification;
use App\Domains\Jobs\Actions\Job\{
    CreateJobAction,
    UpdateJobAction
};

use Illuminate\Support\Facades\Notification;
use App\Domains\Jobs\Requests\Job\{
    StoreJobRequest,
    UpdateJobRequest
};
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Location\Models\City;
use App\Domains\Location\Models\Country;
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
    public $country;
    public $city;
    public $address;
    public $cities = [];
    public $countries = [];
    public $responsibilities;
    public $benefits;
    public $qualifications;
    public $work_type;
    public $work_place;
    public $deadline;
    public $category_id;
    public $is_active = true;

    // public function mount($job = null)
    // {
    //     if ($job) {
    //         $model = JobPost::findOrFail($job);
    //         $this->jobId = $model->id;
    //         $this->fill($model->toArray());

    //         if ($model->location) {
    //             $this->country = $model->location->country_id;
    //             $this->city = $model->location->city_id;
    //             $this->address = $model->location->address;
	// 			// Preload cities when editing existing job
	// 			$this->cities = $this->country
	// 				? City::where('country_id', (int) $this->country)->select('id', 'name')->orderBy('name')->get()->toArray()
	// 				: [];
    //         }
    //     }
    // }




    public function mount($job = null)
    {
        if ($job) {
            $model = JobPost::findOrFail($job);
            $this->jobId = $model->id;
            $this->title = $model->title;
            $this->experience = $model->experience;
            $this->description = $model->description;
            $this->salary_min = $model->salary_min;
            $this->salary_max = $model->salary_max;
            $this->responsibilities = $model->responsibilities;
            $this->benefits = $model->benefits;
            $this->qualifications = $model->qualification;
            $this->work_type = $model->work_type;
            $this->work_place = $model->work_place;
            $this->deadline = $model->deadline ? date('Y-m-d', strtotime($model->deadline)) : null;
            $this->category_id = $model->category_id;
            $this->is_active = $model->is_active;

            // Load location data
            if ($model->location) {
                $this->country = $model->location->country_id;
                $this->city = $model->location->city_id;
                $this->address = $model->location->address;

                // Preload cities when editing existing job
                $this->cities = $this->country
                    ? City::where('country_id', (int) $this->country)->select('id', 'name')->orderBy('name')->get()->toArray()
                    : [];
            }
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

            event(new JobCreated($job));
            $admins = User::role('admin')->get();
            Notification::send($admins, new JobCreatedNotification($job));
            session()->flash('message', 'Job updated successfully!');
        } else {
            $request = new StoreJobRequest();
            $request->merge($this->getJobData());
            $validated = Validator::make($request->all(), $request->rules())->validate();

            $myJob = $create->execute($validated);
            event(new JobCreated($myJob));
            $admins = User::role('admin')->get();
            Notification::send($admins, new JobCreatedNotification($myJob));
            session()->flash('message', 'Job created successfully!');
        }

        return $this->redirectRoute('jobs.index', navigate: true);
    }

    /**
     * Gather the job data for request validation
     */
    protected function getJobData(): array
    {
        return [
            'title'           => $this->title,
            'experience'      => $this->experience,
            'description'     => $this->description,
            'salary_min'      => $this->salary_min,
            'salary_max'      => $this->salary_max,
            'work_type'       => $this->work_type,
            'work_place'      => $this->work_place,
            'deadline'        => $this->deadline,
            'category_id'     => $this->category_id,
            'is_active'       => $this->is_active,
            'responsibilities'=> $this->responsibilities,
            'qualification'   => $this->qualifications,
            'benefits'        => $this->benefits,
            'country_id'      => $this->country,
            'city_id'         => $this->city,
            'address'         => $this->address,
        ];
    }

    public function cancel()
    {
        return $this->redirectRoute('jobs.index', navigate: true);
    }

	// When country changes, reset the cities and reload them
	public function updatedCountry($value)
	{
		$this->city = null;
		$countryId = (int) $value;
		$this->cities = $countryId
			? City::where('country_id', $countryId)->select('id', 'name')->orderBy('name')->get()->toArray()
			: [];
	}


	//handler for country change from the select element.
	public function onCountryChange($value)
	{
		$this->country = (int) $value;
		$this->updatedCountry($this->country);
	}

    public function render()
    {
		// Load countries for the country dropdown
		$this->countries = Country::select('id', 'name')->orderBy('name')->get()->toArray();
		// If country is selected, load cities dynamically
		$this->cities = $this->country
			? City::where('country_id', (int) $this->country)->select('id', 'name')->orderBy('name')->get()->toArray()
			: [];
		return view('livewire.jobs.job-form')->layout('layouts.app');
    }
}
