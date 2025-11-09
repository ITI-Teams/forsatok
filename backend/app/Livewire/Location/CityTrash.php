<?php

namespace App\Livewire\Location;

use App\Domains\Location\Actions\City\DeleteCityAction;
use App\Domains\Location\Actions\City\RestoreCityAction;
use App\Domains\Location\Models\City;
use Livewire\Component;

class CityTrash extends Component
{
    public $trashedCities;

    public function mount()
    {
        $this->loadTrashed();
    }

    public function loadTrashed()
    {
        $this->trashedCities = City::onlyTrashed()->with('country')->latest()->get();
    }

    public function restore($id, RestoreCityAction $restore)
    {
        $restore->execute($id);
        session()->flash('message', '✅ City restored successfully!');
        $this->loadTrashed();
    }

    public function forceDelete($id, DeleteCityAction $forceDelete)
    {
        $forceDelete->execute($id);
        session()->flash('message', '❌ City permanently deleted!');
        $this->loadTrashed();
    }

    public function render()
    {
        return view('livewire.location.city-trash')->layout('layouts.app');
    }
}

