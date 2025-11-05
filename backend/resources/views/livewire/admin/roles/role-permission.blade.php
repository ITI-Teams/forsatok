<div class="container-fluid py-3">

    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-bold text-primary mb-0">
            <i class="fa-solid fa-shield-halved me-2"></i>Assign Permissions to Role
        </h4>
        <a wire:navigate href="{{ route('admin.roles') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Back
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
        <div class="card shadow-sm border-0">
            <div class="card-body">
                <h5 class="fw-semibold text-secondary mb-3">
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

    <!-- Toast Message -->
    @if(session()->has('message'))
        <div class="position-fixed top-0 end-0 p-3" style="z-index:1080;">
            <div class="toast show text-white bg-success border-0 shadow-lg" role="alert">
                <div class="d-flex">
                    <div class="toast-body fw-semibold">
                        <i class="fa-solid fa-circle-check me-2"></i>{{ session('message') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto" data-bs-dismiss="toast"></button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toastEl = document.querySelector('.toast');
                if (toastEl) {
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                    setTimeout(() => toast.hide(), 3000);
                }
            });
        </script>
    @endif

</div>
