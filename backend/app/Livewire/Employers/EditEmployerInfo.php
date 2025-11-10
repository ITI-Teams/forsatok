<?php

namespace App\Livewire\Employers;

use App\Domains\Employers\Actions\GetCurrentEmployerInfoAction;
use App\Domains\Employers\Actions\UpdateEmployerInfoAction;
use App\Domains\Employers\Models\EmployerInfo;
use App\Domains\Employers\Requests\UpdateEmployerInfoRequest;
use App\Domains\Location\Models\Country;
use App\Domains\Location\Models\City;
use App\Domains\Location\Models\Locationable;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Validation\Rules;
use Livewire\Component;

class EditEmployerInfo extends Component
{
    public $company_name, $industry, $about, $website, $email, $phone;
    public $current_password, $password, $password_confirmation;
    public $showPasswordSection = false;
    
    // Location fields
    public $country_id;
    public $city_id;
    public $address;
    public $countries;
    public $cities = [];

    private function domainRules(): array
    {
        return (new UpdateEmployerInfoRequest())->rules();
    }

    private function emailRules(): array
    {
        return ['email' => ['required', 'email', 'max:255']];
    }

    private function domainMessages(): array
    {
        return (new UpdateEmployerInfoRequest())->messages();
    }

    private function emailMessages(): array
    {
        return [
            'email.required' => 'Email is required.',
            'email.email' => 'Please enter a valid email address.',
            'email.max' => 'Email must be at most 255 characters.',
        ];
    }

    public function updated($propertyName): void
    {
        $rules = array_merge($this->domainRules(), $this->emailRules());
        $messages = array_merge($this->domainMessages(), $this->emailMessages());
        $this->validateOnly($propertyName, $rules, $messages);
    }

    public function mount(GetCurrentEmployerInfoAction $getInfo): void
    {
        $this->countries = Country::orderBy('name')->get();
        
        $info = $getInfo->execute(Auth::id());
        $user = Auth::user();

        if ($info) {
            $this->company_name = $info->company_name ?? null;
            $this->industry = $info->industry ?? null;
            $this->about = $info->about ?? null;
            $this->website = $info->website ?? null;
            
            // Load location data
            $locationable = $info->location;
            if ($locationable) {
                $this->country_id = $locationable->country_id;
                $this->city_id = $locationable->city_id;
                $this->address = $locationable->address;
                
                if ($this->country_id) {
                    $this->loadCities();
                }
            }
        }
        
        $this->phone = null; // Phone not stored in employer_infos table
        $this->email = $user->email ?? '';
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

    public function updateProfile(UpdateEmployerInfoAction $action)
    {
        if (! Auth::check()) {
            return $this->redirectRoute('login');
        }

        $rules = array_merge($this->domainRules(), $this->emailRules());
        $messages = array_merge($this->domainMessages(), $this->emailMessages());
        $validated = $this->validate($rules, $messages);

        $info = EmployerInfo::firstOrNew(['user_id' => Auth::id()]);
        if (! $info->exists) {
            $info->user_id = Auth::id();
        }
        $info = $action->execute($info, $validated);
        
        // Refresh to ensure we have the ID
        $info->refresh();

        // Update user email if changed
        $user = Auth::user();
        if ($user->email !== $this->email) {
            $user->email = $this->email;
            $user->email_verified_at = null;
            $user->save();
        }
        
        // Save location
        $this->saveLocation($info);

        session()->flash('message', 'Profile updated successfully!');

        return $this->redirectRoute('employer.profile', navigate: true);
    }
    
    protected function saveLocation(EmployerInfo $employerInfo)
    {
        // Ensure the employer info has an ID
        if (!$employerInfo->id) {
            return;
        }
        
        // Convert empty strings to null
        $countryId = !empty($this->country_id) ? $this->country_id : null;
        $cityId = !empty($this->city_id) ? $this->city_id : null;
        $address = !empty(trim($this->address ?? '')) ? trim($this->address) : null;
        
        if ($countryId || $cityId || $address) {
            Locationable::updateOrCreate(
                [
                    'locationable_id' => $employerInfo->id,
                    'locationable_type' => EmployerInfo::class,
                ],
                [
                    'country_id' => $countryId,
                    'city_id' => $cityId,
                    'address' => $address,
                ]
            );
        } else {
            // Delete location if all fields are empty
            $employerInfo->location()->delete();
        }
    }

    public function updatePassword()
    {
        $this->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'confirmed', Rules\Password::defaults()],
        ], [
            'current_password.required' => 'Current password is required.',
            'password.required' => 'New password is required.',
            'password.confirmed' => 'Password confirmation does not match.',
        ]);

        $user = Auth::user();

        if (!Hash::check($this->current_password, $user->password)) {
            $this->addError('current_password', 'The current password is incorrect.');
            return;
        }

        $user->password = Hash::make($this->password);
        $user->save();

        $this->reset(['current_password', 'password', 'password_confirmation', 'showPasswordSection']);
        session()->flash('password_message', 'Password updated successfully!');
    }

    public function sendPasswordResetLink()
    {
        $user = Auth::user();

        $status = Password::sendResetLink(['email' => $user->email]);

        if ($status != Password::RESET_LINK_SENT) {
            $this->addError('email', __($status));
            return;
        }

        session()->flash('reset_link_message', 'Password reset link has been sent to your email!');
    }

    public function render()
    {
        return view('livewire.employers.edit-employer-info')->layout('layouts.app');
    }
}
