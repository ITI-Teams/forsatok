<?php

namespace App\Livewire\Location;

use App\Domains\Location\Actions\City\CreateCityAction;
use App\Domains\Location\Actions\City\UpdateCityAction;
use App\Domains\Location\Models\City;
use App\Domains\Location\Models\Country;
use App\Domains\Location\Requests\City\StoreCityRequest;
use App\Domains\Location\Requests\City\UpdateCityRequest;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class CityForm extends Component
{
    public $cityId, $name, $countryId;
    public $countries;

    protected $rules = [
        'name' => 'required|string|max:255',
        'countryId' => 'required|exists:countries,id',
    ];

    public function mount($city = null)
    {
        $this->countries = Country::orderBy('name')->get();
        
        if ($city) {
            $model = City::findOrFail($city);
            $this->cityId = $model->id;
            $this->name = $model->name;
            $this->countryId = $model->country_id;
        }
    }

    public function save(CreateCityAction $create, UpdateCityAction $update)
    {
        if ($this->cityId) {
            $request = new UpdateCityRequest();
            $request->merge([
                'name' => $this->name,
                'country_id' => $this->countryId,
            ]);
            $validated = Validator::make($request->all(), $request->rules())->validate();
        } else {
            $request = new StoreCityRequest();
            $request->merge([
                'name' => $this->name,
                'country_id' => $this->countryId,
            ]);
            $validated = Validator::make($request->all(), $request->rules())->validate();
        }

        if ($this->cityId) {
            $city = City::findOrFail($this->cityId);
            $update->execute($city, $validated);
            session()->flash('message', '✅ City updated successfully!');
        } else {
            $create->execute($validated);
            session()->flash('message', '✅ City created successfully!');
        }
        return $this->redirectRoute('cities.index', navigate: true);
    }

    public function cancel()
    {
        return $this->redirectRoute('cities.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.location.city-form')->layout('layouts.app');
    }
}

