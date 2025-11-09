
    <div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-semibold mb-0">
                <i class="fa-solid fa-pen-to-square me-2 text-primary"></i>
                {{ $jobId ? 'Edit Job' : 'Create Job' }}
            </h4>

            <a wire:click.prevent="cancel" href="#" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to List
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body">
                {{-- Success Message --}}
                @if (session()->has('message'))
                    <div class="alert alert-success mb-4">
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit.prevent="save">
                    <div class="row g-4">
                        {{-- Title --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-heading me-2 text-primary"></i>Title
                            </label>
                            <input type="text" wire:model.defer="title" class="form-control" placeholder="Enter title">
                            @error('title') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- Category --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-tags me-2 text-primary"></i>Category
                            </label>
                            <select wire:model.defer="category_id" class="form-select">
                                <option selected disabled>Choose category</option>
                                @foreach(\App\Domains\Jobs\Models\Category::all() as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                @endforeach
                            </select>
                            @error('category_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- Status --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-toggle-on me-2 text-primary"></i>Status
                            </label>
                            <select wire:model.defer="is_active" class="form-select">
                                <option value="1">Active</option>
                                <option value="0">Inactive</option>
                            </select>
                            @error('is_active') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- Deadline --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-calendar me-2 text-primary"></i>Deadline
                            </label>
                            <input type="date" wire:model.defer="deadline" class="form-control">
                            @error('deadline') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- Experience --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-briefcase me-2 text-primary"></i>Experience
                            </label>
                            <input type="text" wire:model.defer="experience" class="form-control" placeholder="e.g. 2 years">
                            @error('experience') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- Country --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-globe me-2 text-primary"></i>Country
                            </label>
                            <select wire:model.live="country_id" class="form-select">
                                <option value="">Select Country</option>
                                @foreach($countries as $country)
                                    <option value="{{ $country->id }}">{{ $country->name }}</option>
                                @endforeach
                            </select>
                            @error('country_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- City --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-city me-2 text-primary"></i>City
                            </label>
                            <select wire:model.defer="city_id" class="form-select" @if(!$country_id) disabled @endif>
                                <option value="">Select City</option>
                                @foreach($cities as $city)
                                    <option value="{{ $city->id }}">{{ $city->name }}</option>
                                @endforeach
                            </select>
                            @error('city_id') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- Address --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-map-marker-alt me-2 text-primary"></i>Address
                            </label>
                            <input type="text" wire:model.defer="address" class="form-control" placeholder="Street, Building, Office">
                            @error('address') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- Salary Range --}}
                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-dollar-sign me-2 text-primary"></i>Salary (Min)
                            </label>
                            <input type="number" wire:model.defer="salary_min" class="form-control" placeholder="Minimum salary" step="0.01" max="99999999.99">
                            @error('salary_min') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        <div class="col-md-6">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-dollar-sign me-2 text-primary"></i>Salary (Max)
                            </label>
                            <input type="number" wire:model.defer="salary_max" class="form-control" placeholder="Maximum salary" step="0.01" max="99999999.99">
                            @error('salary_max') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>

                        {{-- Description --}}
                        <div class="col-12">
                            <label class="form-label fw-semibold">
                                <i class="fa-solid fa-file-lines me-2 text-primary"></i>Description
                            </label>
                            <textarea wire:model.defer="description" class="form-control" rows="4" placeholder="Enter job description..."></textarea>
                            @error('description') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>

                    <div class="mt-4 d-flex justify-content-end gap-2">
                        <button type="button" wire:click="cancel" class="btn btn-outline-secondary px-4">
                            <i class="fa-solid fa-rotate-left me-2"></i>Cancel
                        </button>
                        <button type="submit" class="btn btn-primary px-4">
                            <i class="fa-solid fa-floppy-disk me-2"></i>{{ $jobId ? 'Update' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

