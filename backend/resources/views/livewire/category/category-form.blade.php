<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-tag me-2 text-primary"></i>
            @if ($categoryId) Edit Category @else Create Category @endif
        </h4>
        <a wire:navigate href="{{ route('categories.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
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
                    <div class="col-md-12">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-tag me-2 text-primary"></i>Name
                        </label>
                        <input type="text" wire:model.defer="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter category name">
                        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex flex-wrap justify-content-end gap-2">
                    <button type="button" wire:click="cancel" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-rotate-left me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>
                        @if ($categoryId) Update @else Create @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
