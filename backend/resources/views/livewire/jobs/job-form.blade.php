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
                        @error('title')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Category --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-tags me-2 text-primary"></i>Category
                        </label>
                        <select wire:model.defer="category_id" class="form-select">
                            <option value="">Choose category</option>
                            @foreach (\App\Domains\Jobs\Models\Category::all() as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')<span class="text-danger small">{{ $message }}</span>@enderror
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
                        @error('is_active')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Deadline --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-calendar me-2 text-primary"></i>Deadline
                        </label>
                        <input type="date" wire:model.defer="deadline" class="form-control">
                        @error('deadline')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Experience --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-briefcase me-2 text-primary"></i>Experience
                        </label>
                        <input type="text" wire:model.defer="experience" class="form-control" placeholder="e.g. 2 years">
                        @error('experience')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Location --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-map-marker-alt me-2 text-primary"></i>Location
                        </label>

                        <div class="row">
                            {{-- Country Dropdown --}}
                            <div class="col-md-6 mb-2 mb-md-0">
                                <select wire:model="country" wire:change="onCountryChange($event.target.value)" class="form-select">
                                    <option value="">Select Country</option>
                                    @foreach ($countries as $c)
                                        <option value="{{ data_get($c,'id') }}">{{ data_get($c,'name') }}</option>
                                    @endforeach
                                </select>
                                @error('country')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>

                            {{-- City Dropdown --}}
                            <div class="col-md-6">
                                <select wire:model.defer="city" class="form-select" {{ empty($cities) ? 'disabled' : '' }}>
                                    <option value="">Select City</option>
                                    @foreach ($cities as $c)
                                        <option value="{{ data_get($c,'id') }}">{{ data_get($c,'name') }}</option>
                                    @endforeach
                                </select>
                                @error('city')<span class="text-danger small">{{ $message }}</span>@enderror
                            </div>
                        </div>

                        {{-- Optional Address --}}
                        <input type="text" wire:model.defer="address" class="form-control mt-2" placeholder="Street/office address">
                        @error('address')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Salary Range --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-dollar-sign me-2 text-primary"></i>Salary (Min)
                        </label>
                        <input type="number" wire:model.defer="salary_min" class="form-control" placeholder="Minimum salary">
                        @error('salary_min')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-dollar-sign me-2 text-primary"></i>Salary (Max)
                        </label>
                        <input type="number" wire:model.defer="salary_max" class="form-control" placeholder="Maximum salary">
                        @error('salary_max')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Work Type --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-briefcase-clock me-2 text-primary"></i>Work Type
                        </label>
                        <select wire:model.defer="work_type" class="form-select">
                            <option value="">Choose work type</option>
                            <option value="full-time">Full-time</option>
                            <option value="part-time">Part-time</option>
                            <option value="freelance">Freelance</option>
                        </select>
                        @error('work_type')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Work Place --}}
                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-building me-2 text-primary"></i>Work Place
                        </label>
                        <select wire:model.defer="work_place" class="form-select">
                            <option value="">Choose work place</option>
                            <option value="on-site">On-site</option>
                            <option value="remote">Remote</option>
                            <option value="hybrid">Hybrid</option>
                        </select>
                        @error('work_place')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Description --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-file-lines me-2 text-primary"></i>Description
                        </label>
                        <textarea wire:model.defer="description" class="form-control" rows="4" placeholder="Enter job description..."></textarea>
                        @error('description')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Responsibilities --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-list-check me-2 text-primary"></i>Responsibilities
                        </label>
                        <textarea wire:model.defer="responsibilities" class="form-control" rows="3" placeholder="List key responsibilities..."></textarea>
                        @error('responsibilities')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Qualifications --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-user-graduate me-2 text-primary"></i>Qualifications
                        </label>
                        <textarea wire:model.defer="qualifications" class="form-control" rows="3" placeholder="List required qualifications..."></textarea>
                        @error('qualifications')<span class="text-danger small">{{ $message }}</span>@enderror
                    </div>

                    {{-- Benefits --}}
                    <div class="col-12">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-gift me-2 text-primary"></i>Benefits
                        </label>
                        <textarea wire:model.defer="benefits" class="form-control" rows="3" placeholder="List job benefits..."></textarea>
                        @error('benefits')<span class="text-danger small">{{ $message }}</span>@enderror
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
