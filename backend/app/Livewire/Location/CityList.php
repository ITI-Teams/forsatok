<?php

namespace App\Livewire\Location;

use App\Domains\Location\Actions\City\SoftDeleteCityAction;
use App\Domains\Location\Models\City;
use Livewire\Component;

class CityList extends Component
{
    public $cities;
    public $search = '';

    public function mount()
    {
        $this->loadCities();
    }

    public function loadCities()
    {
        $query = City::with('country')->latest();
        
        if ($this->search) {
            $query->where('name', 'like', '%' . $this->search . '%')
                  ->orWhereHas('country', function($q) {
                      $q->where('name', 'like', '%' . $this->search . '%');
                  });
        }
        
        $this->cities = $query->get();
    }

    public function updatedSearch()
    {
        $this->loadCities();
    }

    public function delete($id, SoftDeleteCityAction $delete)
    {
        $city = City::findOrFail($id);
        $delete->execute($city);

        session()->flash('message', '🗑️ City moved to trash!');
        $this->loadCities();
    }

    public function render()
    {
        return view('livewire.location.city-list')->layout('layouts.app');
    }
}

