<?php

namespace App\Livewire\Location;

use Livewire\Component;

class LocationIndex extends Component
{
    public $activeTab = 'countries';

    public function setTab($tab)
    {
        $this->activeTab = $tab;
    }

    public function mount()
    {
        // Set default tab based on route
        if (request()->routeIs('cities.*')) {
            $this->activeTab = 'cities';
        } else {
            $this->activeTab = 'countries';
        }
    }

    public function render()
    {
        return view('livewire.location.location-index')->layout('layouts.app');
    }
}

