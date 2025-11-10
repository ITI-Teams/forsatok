<?php

namespace App\Livewire\Location;

use App\Domains\Location\Actions\Country\CreateCountryAction;
use App\Domains\Location\Actions\Country\UpdateCountryAction;
use App\Domains\Location\Models\Country;
use App\Domains\Location\Requests\Country\StoreCountryRequest;
use App\Domains\Location\Requests\Country\UpdateCountryRequest;
use Illuminate\Support\Facades\Validator;
use Livewire\Component;

class CountryForm extends Component
{
    public $countryId, $name, $code;

    protected $rules = [
        'name' => 'required|string|max:255',
        'code' => 'nullable|string|max:3',
    ];

    public function mount($country = null)
    {
        if ($country) {
            $model = Country::findOrFail($country);
            $this->countryId = $model->id;
            $this->name = $model->name;
            $this->code = $model->code;
        }
    }

    public function save(CreateCountryAction $create, UpdateCountryAction $update)
    {
        if ($this->countryId) {
            $request = new UpdateCountryRequest();
            $request->merge([
                'name' => $this->name,
                'code' => $this->code,
                'country_id' => $this->countryId,
            ]);
            $validated = Validator::make($request->all(), $request->rules())->validate();
        } else {
            $request = new StoreCountryRequest();
            $request->merge([
                'name' => $this->name,
                'code' => $this->code,
            ]);
            $validated = Validator::make($request->all(), $request->rules())->validate();
        }

        if ($this->countryId) {
            $country = Country::findOrFail($this->countryId);
            $update->execute($country, $validated);
            session()->flash('message', '✅ Country updated successfully!');
        } else {
            $create->execute($validated);
            session()->flash('message', '✅ Country created successfully!');
        }
        return $this->redirectRoute('countries.index', navigate: true);
    }

    public function cancel()
    {
        return $this->redirectRoute('countries.index', navigate: true);
    }

    public function render()
    {
        return view('livewire.location.country-form')->layout('layouts.app');
    }
}

