<?php

namespace App\Livewire\Employers;

use App\Domains\Employers\Actions\GetCurrentEmployerInfoAction;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EmployerProfile extends Component
{
    public $company_name, $industry, $about, $website, $email, $phone;
    public $average_rating, $total_reviews;
    public $location_display;

    public function mount(GetCurrentEmployerInfoAction $getInfo): void
    {
        if (! Auth::check()) {
            $this->redirectRoute('login');
            return;
        }

        $info = $getInfo->execute(Auth::id());
        $user = Auth::user();

        if ($info) {
            $this->company_name = $info->company_name;
            $this->industry = $info->industry;
            $this->about = $info->about;
            $this->website = $info->website;
            $this->phone = null; // Phone not stored in employer_infos table
            $this->average_rating = $info->average_rating;
            $this->total_reviews = $info->total_reviews;
            
            // Format location display
            if ($info->location) {
                $locationParts = [];
                if ($info->location->city) {
                    $locationParts[] = $info->location->city->name;
                }
                if ($info->location->country) {
                    $locationParts[] = $info->location->country->name;
                }
                if ($info->location->address) {
                    $locationParts[] = $info->location->address;
                }
                $this->location_display = !empty($locationParts) ? implode(' - ', $locationParts) : 'N/A';
            } else {
                $this->location_display = 'N/A';
            }
        } else {
            $this->company_name = null;
            $this->industry = null;
            $this->about = null;
            $this->website = null;
            $this->phone = null;
            $this->location_display = 'N/A';
            $this->average_rating = 0;
            $this->total_reviews = 0;
        }

        $this->email = $user->email ?? null;
    }

    public function render()
    {
        return view('livewire.employers.employer-profile')->layout('layouts.app');
    }
}
