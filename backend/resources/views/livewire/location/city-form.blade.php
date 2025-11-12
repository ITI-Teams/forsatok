<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-city me-2 text-primary"></i>
            @if ($cityId) Edit City @else Create City @endif
        </h4>
        <a wire:navigate href="{{ route('cities.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
            <i class="fa-solid fa-arrow-left me-2"></i> Back to List
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body">
            @if (session('message'))
                <div class="alert alert-success">{{ session('message') }}</div>
            @endif

            <form wire:submit.prevent="save">
                <div class="row g-4">
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-city me-2 text-primary"></i>Name
                        </label>
                        <input type="text" wire:model.defer="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter city name">
                        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-globe me-2 text-primary"></i>Country
                        </label>
                        <select wire:model.defer="countryId"
                                class="form-select @error('countryId') is-invalid @enderror">
                            <option value="">Select a country</option>
                            @foreach($countries as $country)
                                <option value="{{ $country->id }}">{{ $country->name }}</option>
                            @endforeach
                        </select>
                        @error('countryId') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex flex-wrap justify-content-end gap-2">
                    <button type="button" wire:click="cancel" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-rotate-left me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>
                        @if ($cityId) Update @else Create @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

