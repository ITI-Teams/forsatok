<?php

namespace App\Livewire\Location;

use App\Domains\Location\Actions\City\SoftDeleteCityAction;
use App\Domains\Location\Models\City;
use Livewire\Component;
use Livewire\WithPagination;
use Livewire\Attributes\On;

class CityList extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $search = '';
    public $searchFields = ['name', 'country.name'];
    public $perPage = 5;

    #[On('citySearchUpdated')]
    public function handleSearch($payload)
    {
        $this->search = $payload['query'] ?? '';
        $this->searchFields = $payload['fields'] ?? ['name', 'country.name'];
        $this->resetPage();
    }

    public function delete($id, SoftDeleteCityAction $delete)
    {
        $city = City::findOrFail($id);
        $delete->execute($city);

        session()->flash('message', '🗑️ City moved to trash!');
    }

    public function render()
    {
        $query = City::with('country')->latest();

        if ($this->search && count($this->searchFields) > 0) {
            $query->where(function ($q) {
                foreach ($this->searchFields as $i => $field) {
                    if (str_contains($field, '.')) {
                        [$relation, $col] = explode('.', $field);
                        if ($i === 0) {
                            $q->whereHas($relation, fn($q2) => $q2->where($col, 'like', "%{$this->search}%"));
                        } else {
                            $q->orWhereHas($relation, fn($q2) => $q2->where($col, 'like', "%{$this->search}%"));
                        }
                    } else {
                        if ($i === 0) {
                            $q->where($field, 'like', "%{$this->search}%");
                        } else {
                            $q->orWhere($field, 'like', "%{$this->search}%");
                        }
                    }
                }
            });
        }

        $cities = $query->paginate($this->perPage);

        return view('livewire.location.city-list', [
            'cities' => $cities,
        ])->layout('layouts.app');
    }
}
