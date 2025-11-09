<?php

namespace App\Livewire\Location;

use App\Domains\Location\Actions\Country\SoftDeleteCountryAction;
use App\Domains\Location\Models\Country;
use Livewire\Component;

class CountryList extends Component
{
    public $countries;
    public $search = '';

    public function mount()
    {
        $this->loadCountries();
    }

    public function loadCountries()
    {
        $query = Country::latest();
        
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhere('code', 'like', '%' . $this->search . '%');
        }
        
        $this->countries = $query->get();
    }

    public function updatedSearch()
    {
        $this->loadCountries();
    }

    public function delete($id, SoftDeleteCountryAction $delete)
    {
        $country = Country::findOrFail($id);
        $delete->execute($country);

        session()->flash('message', '🗑️ Country moved to trash!');
        $this->loadCountries();
    }

    public function render()
    {
        return view('livewire.location.country-list')->layout('layouts.app');
    }
}

