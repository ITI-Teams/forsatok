{{-- <x-app-layout> --}}
<div class="container-fluid py-2">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-user-pen me-2 text-primary"></i>
            @if ($userId) Edit User @else Create User @endif
        </h4>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary d-flex align-items-center">
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
                            <i class="fa-solid fa-circle-user me-2 text-primary"></i>Name
                        </label>
                        <input type="text" wire:model.defer="name"
                            class="form-control @error('name') is-invalid @enderror"
                            placeholder="Enter name">
                        @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-envelope me-2 text-primary"></i>Email
                        </label>
                        <input type="email" wire:model.defer="email"
                            class="form-control @error('email') is-invalid @enderror"
                            placeholder="Enter email">
                        @error('email') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-lock me-2 text-primary"></i>Password
                        </label>
                        <input type="password" wire:model.defer="password"
                            class="form-control @error('password') is-invalid @enderror"
                            placeholder="Enter password">
                        @error('password') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-lock me-2 text-primary"></i>Confirm Password
                        </label>
                        <input type="password" wire:model.defer="password_confirmation"
                            class="form-control" placeholder="Confirm password">
                    </div>

                    <div class="col-md-6">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-user-tag me-2 text-primary"></i>Type
                        </label>
                        <select wire:model.defer="type"
                            class="form-select @error('type') is-invalid @enderror">
                            <option value="">Select type</option>
                            <option value="admin">Admin</option>
                            <option value="employer">Employer</option>
                            <option value="candidate">Candidate</option>
                        </select>
                        @error('type') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
                    </div>
                </div>

                <div class="mt-4 d-flex flex-wrap justify-content-end gap-2">
                    <button type="button" wire:click="cancel" class="btn btn-outline-secondary px-4">
                        <i class="fa-solid fa-rotate-left me-2"></i>Cancel
                    </button>
                    <button type="submit" class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>
                        @if ($userId) Update @else Create @endif
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
{{-- </x-app-layout> --}}