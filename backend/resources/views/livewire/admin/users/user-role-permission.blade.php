<div>
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-shield-halved me-2 text-primary"></i>Assign Roles & Permissions
        </h4>
        <a wire:navigate href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i>Back to Users
        </a>
    </div>
    <!-- Success Toast -->
    @if (session()->has('message'))
        <div id="success-toast"
             class="position-fixed top-0 end-0 p-3"
             style="z-index: 1080;">
            <div class="toast show align-items-center text-white bg-success border-0 shadow-lg"
                 role="alert"
                 aria-live="assertive"
                 aria-atomic="true"
                 data-bs-delay="3000"
                 style="min-width: 280px;">
                <div class="d-flex">
                    <div class="toast-body fw-semibold">
                        <i class="fa-solid fa-circle-check me-2"></i>{{ session('message') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"
                            aria-label="Close"></button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toastEl = document.querySelector('#success-toast .toast');
                if (toastEl) {
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                    setTimeout(() => {
                        toast.hide();
                    }, 3000);
                }
            });
        </script>
    @endif

    <!-- Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <!-- Select User -->
            <div class="mb-4">
                <label class="form-label fw-semibold">
                    <i class="fa-solid fa-user-check me-2 text-primary"></i>Select User
                </label>
                <select wire:model.live="user_id" class="form-select">
                    <option value="">-- Choose User --</option>
                    @foreach($users as $user)
                        <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                    @endforeach
                </select>
            </div>

            @if($user_id)
                <div class="row g-4">
                    <!-- Roles -->
                    <div class="col-md-6">
                        <h5 class="fw-semibold mb-3 text-primary">
                            <i class="fa-solid fa-user-shield me-2"></i>Roles
                        </h5>
                        <div class="border rounded p-3" style="min-height: 200px;">
                            @foreach($roles as $role)
                                <div class="form-check mb-2">
                                    <input type="checkbox"
                                           wire:model="selectedRoles"
                                           value="{{ $role->name }}"
                                           id="role_{{ $role->id }}"
                                           class="form-check-input">
                                    <label class="form-check-label" for="role_{{ $role->id }}">
                                        {{ ucfirst($role->name) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Permissions -->
                    <div class="col-md-6">
                        <h5 class="fw-semibold mb-3 text-primary">
                            <i class="fa-solid fa-key me-2"></i>Permissions
                        </h5>
                        <div class="border rounded p-3" style="min-height: 200px;">
                            @foreach($permissions as $perm)
                                <div class="form-check mb-2">
                                    <input type="checkbox"
                                           wire:model="selectedPermissions"
                                           value="{{ $perm->name }}"
                                           id="perm_{{ $perm->id }}"
                                           class="form-check-input">
                                    <label class="form-check-label" for="perm_{{ $perm->id }}">
                                        {{ ucfirst($perm->name) }}
                                    </label>
                                </div>
                            @endforeach
                        </div>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="mt-4 d-flex justify-content-end gap-2">
                    <button wire:click="updateUserRolesPermissions"
                            class="btn btn-primary px-4">
                        <i class="fa-solid fa-floppy-disk me-2"></i>Save Changes
                    </button>
                </div>
            @endif


        </div>
    </div>
</div>



