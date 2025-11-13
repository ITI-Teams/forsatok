<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-wrench me-2 text-primary"></i>
            @if ($skillId) Edit Skill @else Create Skill @endif
        </h4>
        <a wire:navigate href="{{ route('skills.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
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
                            <i class="fa-solid fa-gear me-2 text-primary"></i>Skill Name
                        </label>
                        <input type="text" wire:model.defer="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter skill name">
                        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-tag me-2 text-primary"></i>Category
                        </label>
                        <select wire:model="category_id" class="form-select @error('category_id') is-invalid @enderror">
                            <option value="">-- Select Category --</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex flex-wrap justify-content-end gap-2">
                    <button type="button" wire:click="cancel" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-rotate-left me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>
                        @if ($skillId) Update @else Create @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
