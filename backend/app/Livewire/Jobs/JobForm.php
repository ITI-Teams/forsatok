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

    public function save(CreateJobAction $create, UpdateJobAction $update)
    {
        // Choose request type
        if ($this->jobId) {
            $request = new UpdateJobRequest();
            $request->merge($this->getJobData());
            $validated = Validator::make($request->all(), $request->rules())->validate();

            $job = JobPost::findOrFail($this->jobId);
            $update->execute($job, $validated);
            
            // Save location
            $this->saveLocation($job);
            
            session()->flash('message', '✅ Job updated successfully!');
        } else {
            $request = new StoreJobRequest();
            $request->merge($this->getJobData());
            $validated = Validator::make($request->all(), $request->rules())->validate();

            $job = $create->execute($validated);
            
            // Save location
            $this->saveLocation($job);
            
            session()->flash('message', '✅ Job created successfully!');
        }

        return $this->redirectRoute('jobs.index', navigate: true);
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
