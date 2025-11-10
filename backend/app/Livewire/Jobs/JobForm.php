<?php

namespace App\Livewire\Jobs;

use App\Domains\Jobs\Actions\job\{
    CreateJobAction,
    UpdateJobAction
};

use App\Domains\Jobs\Requests\Job\{
    StoreJobRequest,
    UpdateJobRequest
};
use App\Domains\Jobs\Models\JobPost;
use App\Domains\Location\Models\Country;
use App\Domains\Location\Models\City;
use App\Domains\Location\Models\Locationable;
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

    // Location fields
    public $country_id;
    public $city_id;
    public $address;
    public $countries;
    public $cities = [];

    public function mount($job = null)
    {
        $this->countries = Country::orderBy('name')->get();

        if ($job) {
            $model = JobPost::findOrFail($job);
            $this->jobId = $model->id;
            $this->fill($model->toArray());

            // Load location data
            $locationable = $model->location;
            if ($locationable) {
                $this->country_id = $locationable->country_id;
                $this->city_id = $locationable->city_id;
                $this->address = $locationable->address;

                if ($this->country_id) {
                    $this->loadCities();
                }
            }
        }
    }

    public function updatedCountryId()
    {
        $this->city_id = null;
        $this->loadCities();
    }

    public function loadCities()
    {
        if ($this->country_id) {
            $this->cities = City::where('country_id', $this->country_id)
                ->orderBy('name')
                ->get();
        } else {
            $this->cities = [];
        }
    }

    // Comment To Test
    // public function save(CreateJobAction $create, UpdateJobAction $update)
    // {

    //     // Choose request type
    //     if ($this->jobId) {
    //         $request = new UpdateJobRequest();
    //         $request->merge($this->getJobData());
    //         $validated = Validator::make($request->all(), $request->rules())->validate();

    //         $job = JobPost::findOrFail($this->jobId);
    //         $update->execute($job, $validated);

    //         // Save location
    //         $this->saveLocation($job);

    //         session()->flash('message', '✅ Job updated successfully!');
    //     } else {
    //         $request = new StoreJobRequest();
    //         $request->merge($this->getJobData());
    //         $validated = Validator::make($request->all(), $request->rules())->validate();

    //         $job = $create->execute($validated);

    //         // Save location
    //         $this->saveLocation($job);

    //         session()->flash('message', '✅ Job created successfully!');
    //     }

    //     dd($this->getJobData());

    //     return $this->redirectRoute('jobs.index', navigate: true);
    // }

    public function save(CreateJobAction $create, UpdateJobAction $update)
    {
        logger('--- Step 0: Enter save method ---');

        // Step 1: جمع بيانات الوظيفة
        $jobData = $this->getJobData();
        logger('--- Step 1: Job data ---', $jobData);
        logger('--- Step 1b: Location data ---', [
            'country_id' => $this->country_id,
            'city_id' => $this->city_id,
            'address' => $this->address,
        ]);

        try {
            // Step 2: Validation
            if ($this->jobId) {
                $request = new UpdateJobRequest();
            } else {
                $request = new StoreJobRequest();
            }

            $request->merge($jobData);

            logger('--- Step 2: Request data before validation ---', $request->all());

            $validated = Validator::make($request->all(), $request->rules())->validate();

            logger('--- Step 2: Validation passed ---', $validated);

        } catch (\Illuminate\Validation\ValidationException $e) {
            logger('--- Validation Failed ---', $e->errors());
            session()->flash('message', '⚠️ Validation Failed. Check logs.');
            return;
        }

        try {
            // Step 3: Create or Update
            if ($this->jobId) {
                $job = JobPost::findOrFail($this->jobId);
                $update->execute($job, $validated);
                logger('--- Step 3: Job updated ---', ['id' => $job->id]);
            } else {
                $job = $create->execute($validated);
                logger('--- Step 3: Job created ---', ['id' => $job->id]);
            }

            // Step 4: Save location
            $this->saveLocation($job);
            logger('--- Step 4: Location saved ---');

            // Step 5: Success message
            session()->flash('message', $this->jobId ? '✅ Job updated successfully!' : '✅ Job created successfully!');

            // Step 6: Redirect
            return $this->redirectRoute('jobs.index', navigate: true);

        } catch (\Exception $e) {
            logger('--- Step 3/4 Failed ---', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);
            session()->flash('message', '⚠️ Something went wrong. Check logs.');
        }
    }


    protected function saveLocation(JobPost $job)
    {
        // Convert empty strings to null
        $countryId = !empty($this->country_id) ? $this->country_id : null;
        $cityId = !empty($this->city_id) ? $this->city_id : null;
        $address = !empty(trim($this->address ?? '')) ? trim($this->address) : null;

        if ($countryId || $cityId || $address) {
            Locationable::updateOrCreate(
                [
                    'locationable_id' => $job->id,
                    'locationable_type' => JobPost::class,
                ],
                [
                    'country_id' => $countryId,
                    'city_id' => $cityId,
                    'address' => $address,
                ]
            );
        } else {
            // Delete location if all fields are empty
            $job->location()->delete();
        }
    }

    /**
     * Gather the job data for request validation
     */
    protected function getJobData(): array
    {
        return [
            'title' => $this->title,
            'experience' => $this->experience,
            'description' => $this->description,
            'salary_min' => $this->salary_min,
            'salary_max' => $this->salary_max,
            'type' => $this->type,
            'deadline' => $this->deadline,
            'category_id' => $this->category_id,
            'is_active' => $this->is_active,
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
