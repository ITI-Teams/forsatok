<div class="py-4 px-3" data-bs-theme="auto">
    <div class="container">

        <!-- Main Card -->
        <div class="card border-0 shadow-sm rounded-3">

            <!-- Header -->
            <div class="card-header d-flex align-items-center justify-content-between">
                <div>
                    <h2 class="h5 mb-0 fw-bold text-body">
                        {{ $cityId ? 'Edit City' : 'Create New City' }}
                    </h2>
                    <small class="text-secondary">
                        {{ $cityId ? 'Update the city information' : 'Fill in the details to add a new city' }}
                    </small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-body-tertiary"
                     style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-city text-secondary"></i>
                </div>
            </div>

            <!-- Form Section -->
            <div class="card-body">

                @if (session()->has('message'))
                    <div class="alert alert-success d-flex align-items-center gap-2 small mb-4">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit.prevent="save">

                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Name <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <i class="fa-solid fa-city position-absolute"
                               style="left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--bs-secondary-color);"></i>
                            <input type="text"
                                   wire:model.defer="name"
                                   class="form-control ps-5 @error('name') is-invalid @enderror"
                                   placeholder="Enter city name">
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Country -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Country <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <i class="fa-solid fa-globe position-absolute"
                               style="left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--bs-secondary-color); z-index: 1;"></i>
                            <select wire:model.defer="countryId"
                                    class="form-select ps-5 @error('countryId') is-invalid @enderror">
                                <option value="">Select a country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        @error('countryId')
                            <div class="invalid-feedback d-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-center gap-2 pt-3 border-top">
                        <button type="button" wire:click="cancel"
                                class="btn btn-outline-secondary fw-semibold px-4">
                            Cancel
                        </button>
                        <button type="submit"
                                class="btn btn-primary fw-semibold px-4">
                            {{ $cityId ? 'Update' : 'Create' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>
    </div>
</div>

