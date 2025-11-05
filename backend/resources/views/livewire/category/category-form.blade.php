<div class="py-4 px-3">
    <div class="container">

        <!-- Main Card -->
        <div class="card border-0 shadow-sm rounded-3">

            <!-- Header -->
            <div class="px-4 py-3 border-bottom d-flex align-items-center justify-content-between"
                 style="background-color: #f8f9fa;">
                <div>
                    <h2 class="h5 mb-0 fw-bold text-dark">
                        {{ $categoryId ? 'Edit Category' : 'Create New Category' }}
                    </h2>
                    <small class="text-muted">
                        {{ $categoryId ? 'Update the category information' : 'Fill in the details to add a new category' }}
                    </small>
                </div>
                <div class="rounded-circle d-flex align-items-center justify-content-center"
                     style="width: 40px; height: 40px; background-color: #e9ecef;">
                    <i class="fa-solid fa-tag text-secondary"></i>
                </div>
            </div>

            <!-- Form Section -->
            <div class="card-body p-4">

                @if (session()->has('message'))
                    <div class="alert alert-success d-flex align-items-center gap-2 small">
                        <i class="fa-solid fa-circle-check"></i>
                        {{ session('message') }}
                    </div>
                @endif

                <form wire:submit.prevent="save">

                    <!-- Name -->
                    <div class="mb-3">
                        <label class="form-label small fw-semibold">Name <span class="text-danger">*</span></label>
                        <div class="position-relative">
                            <i class="fa-solid fa-tag position-absolute"
                               style="left: 0.9rem; top: 50%; transform: translateY(-50%); color: #adb5bd;"></i>
                            <input type="text"
                                wire:model.defer="name"
                                class="form-control ps-5 @error('name') is-invalid @enderror"
                                placeholder="Enter category name">
                        </div>
                        @error('name')
                            <div class="invalid-feedback d-flex align-items-center gap-1">
                                <i class="fa-solid fa-circle-exclamation"></i> {{ $message }}
                            </div>
                        @enderror
                    </div>

                    <!-- Buttons -->
                    <div class="d-flex justify-content-center gap-2 pt-3 border-top">
                        <button type="button" wire:click="cancel"
                            class="btn btn-light border fw-semibold px-4">
                            Cancel
                        </button>
                        <button type="submit"
                            class="btn btn-primary fw-semibold px-4">
                            {{ $categoryId ? 'Update' : 'Create' }}
                        </button>
                    </div>

                </form>
            </div>
        </div>

    </div>
</div>
