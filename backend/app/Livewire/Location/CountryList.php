<?php

namespace App\Livewire\Location;

use App\Domains\Location\Actions\Country\SoftDeleteCountryAction;
use App\Domains\Location\Models\Country;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class CountryList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $searchFields = ['name', 'code'];
    public $perPage = 2;

    #[On('countrySearchUpdated')]
    public function handleSearch($payload)
    {
        $this->search = $payload['query'] ?? '';
        $this->searchFields = $payload['fields'] ?? ['name', 'code'];
        $this->resetPage();
    }

    public function delete($id, SoftDeleteCountryAction $delete)
    {
        $country = Country::findOrFail($id);
        $delete->execute($country);

        session()->flash('message', '🗑️ Country moved to trash!');
    }

    public function render()
    {
        $query = Country::latest();

        if ($this->search && count($this->searchFields) > 0) {
            $query->where(function ($q) {
                foreach ($this->searchFields as $i => $field) {
                    if ($i === 0) {
                        $q->where($field, 'like', "%{$this->search}%");
                    } else {
                        $q->orWhere($field, 'like', "%{$this->search}%");
                    }
                }
            });
        }

        $countries = $query->paginate($this->perPage);

        return view('livewire.location.country-list', [
            'countries' => $countries,
        ])->layout('layouts.app');
    }
}
