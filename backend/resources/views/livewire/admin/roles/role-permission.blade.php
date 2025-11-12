<div class="container-fluid px-3 px-md-4 py-3 bg-body text-body">
    @if(session()->has('message'))
        <div class="alert alert-success d-flex align-items-center fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('message') }}
        </div>
    @endif

    <!-- Header -->
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-shield-halved me-2 text-primary"></i> Assign Permissions to Role
        </h4>
        <a wire:navigate href="{{ route('admin.roles') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Back
        </a>
    </div>

    <!-- Role Selector -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <label class="form-label fw-semibold mb-2">
                <i class="fa-solid fa-user-shield me-2 text-primary"></i>Select Role
            </label>
            <select wire:model.live="roleId" class="form-select">
                <option value="">-- Choose Role --</option>
                @foreach($roles as $role)
                    <option value="{{ $role->id }}">{{ ucfirst($role->name) }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <!-- Permissions List -->
    @if($roleId)
        <div class="card shadow-sm border border-body bg-body text-body rounded-3">
            <div class="card-body">
                <h5 class="fw-semibold mb-3">
                    <i class="fa-solid fa-key me-2 text-primary"></i>Permissions
                </h5>

                <div class="row">
                    @foreach($permissions as $perm)
                        <div class="col-md-3 col-sm-4 col-6 mb-2">
                            <div class="form-check form-switch">
                                <input type="checkbox"
                                       wire:model="selectedPermissions"
                                       value="{{ $perm->name }}"
                                       class="form-check-input"
                                       id="perm_{{ $loop->index }}">
                                <label class="form-check-label" for="perm_{{ $loop->index }}">
                                    {{ ucfirst($perm->name) }}
                                </label>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="d-flex justify-content-end mt-4">
                    <button wire:click="updateRolePermissions" class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
