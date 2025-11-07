<div class="py-4 px-3">
    <div class="container">
        <div class="card border-0 shadow-sm rounded-3 bg-body text-body">

            <!-- Header -->
            <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between bg-body-tertiary">
                <div>
                    <h2 class="h5 mb-0 fw-bold text-body"> 
                        {{ $skillId ? 'Edit Skill' : 'Create New Skill' }}
                    </h2>
                    <small class="text-secondary">
                        {{ $skillId ? 'Update skill details' : 'Fill in skill information to add' }}
                    </small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center bg-body-secondary" style="width: 40px; height: 40px;">
                    <i class="fa-solid fa-wrench text-secondary"></i>
                </div>
            </div>

            <!-- Form Content -->
            <div class="card-body p-4">
                @if (session()->has('message'))
                    <div class="alert alert-success small d-flex align-items-center gap-2">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit.prevent="save">
                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Skill Name <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <i class="fa-solid fa-gear position-absolute" style="left: 0.9rem; top: 50%; transform: translateY(-50%); color: var(--bs-secondary-color);"></i>
                            <input type="text"
                                   wire:model.defer="name"
                                   class="form-control ps-5 @error('name') is-invalid @enderror"
                                   placeholder="Enter skill name">
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </div>
                        @enderror
                    </div> 

                    <!-- Category -->
                    <div class="mb-3">
                        <label class="form-label fw-semibold small">Category <span class="text-danger">*</span></label>
                        <select wire:model="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-center gap-2 pt-3 border-top">
                        <button type="button" wire:click="cancel" class="btn btn-outline-secondary fw-semibold px-4">
                            Cancel
                        </button>
                        <button type="submit" class="btn btn-primary fw-semibold px-4">
                            {{ $skillId ? 'Update' : 'Create' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
