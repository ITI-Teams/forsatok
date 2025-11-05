<?php

namespace App\Livewire\Employers;

use App\Domains\Employers\Models\EmployerInfo;
use Illuminate\Support\Facades\Auth;
use Livewire\Component;

class EmployerProfile extends Component
{
    public $company_name, $industry, $location, $about, $website, $email, $phone;
    public $average_rating, $total_reviews;

    public function mount(): void
    {
        if (! Auth::check()) {
            $this->redirectRoute('login');
            return;
        }
        $info = EmployerInfo::with('reviews')->where('user_id', Auth::id())->first();
        $user = Auth::user();

        if ($info) {
            $this->company_name = $info->company_name;
            $this->industry = $info->industry;
            $this->location = $info->location;
            $this->about = $info->about;
            $this->website = $info->website;
            $this->phone = null; // Phone not stored in employer_infos table
            $this->average_rating = $info->average_rating;
            $this->total_reviews = $info->total_reviews;
        } else {
            $this->company_name = null;
            $this->industry = null;
            $this->location = null;
            $this->about = null;
            $this->website = null;
            $this->phone = null;
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
