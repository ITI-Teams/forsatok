<?php

namespace App\Livewire\Location;

use App\Domains\Location\Actions\Country\DeleteCountryAction;
use App\Domains\Location\Actions\Country\RestoreCountryAction;
use App\Domains\Location\Models\Country;
use Livewire\Component;

class CountryTrash extends Component
{
    public $trashedCountries;

    public function mount()
    {
        $this->loadTrashed();
    }

    public function loadTrashed()
    {
        $this->trashedCountries = Country::onlyTrashed()->latest()->get();
    }

    public function restore($id, RestoreCountryAction $restore)
    {
        $restore->execute($id);
        session()->flash('message', '✅ Country restored successfully!');
        $this->loadTrashed();
    }

    public function forceDelete($id, DeleteCountryAction $forceDelete)
    {
        $forceDelete->execute($id);
        session()->flash('message', '❌ Country permanently deleted!');
        $this->loadTrashed();
    }

    public function render()
    {
        return view('livewire.location.country-trash')->layout('layouts.app');
    }
}

