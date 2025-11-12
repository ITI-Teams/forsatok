<div class="container-fluid px-3 px-md-4 py-3 bg-body text-body">
    @if (session()->has('message'))
        <div class="alert alert-success d-flex align-items-center fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('message') }}
        </div>
    @endif

    <!-- Header -->
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-key me-2 text-primary"></i> Permissions Management
        </h4>
    </div>

    <!-- Form Card -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
                <div class="row g-3">
                    <div class="col-md-8">
                        <label class="form-label fw-semibold">
                            <i class="fa-solid fa-tag me-2 text-primary"></i>Permission Name
                        </label>
                        <input type="text" wire:model="name" placeholder="Enter permission name"
                            class="form-control @error('name') is-invalid @enderror">
                    </div>
                    <div class="col-md-4 d-flex align-items-end gap-2">
                        <button type="submit" class="btn btn-primary px-4 w-100">
                            <i class="fa-solid fa-floppy-disk me-2"></i>
                            {{ $updateMode ? 'Update' : 'Create' }}
                        </button>
                        @if($updateMode)
                            <button type="button" wire:click="resetInput" class="btn btn-outline-secondary px-4">
                                <i class="fa-solid fa-times me-2"></i>Cancel
                            </button>
                        @endif
                    </div>
                </div>
                @error('name') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror
            </form>
        </div>
    </div>

    <!-- Permissions Table -->
    <div class="card shadow-sm border border-body bg-body text-body rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="border-bottom">
                            <th>#</th>
                            <th>Permission Name</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($permissions as $permission)
                            <tr>
                                <td>{{ $permission->id }}</td>
                                <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary">
                                        {{ ucfirst($permission->name) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-warning me-2" wire:click="edit({{ $permission->id }})">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </button>
                                    <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $permission->id }})"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">
                                    No permissions found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="p-3">
        {{ $permissions->links() }}
    </div>

    <!-- ✅ مودال التأكيد على الحذف -->
    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel"
        aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fa fa-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                        aria-label="Close"></button>
                </div>
                <div class="modal-body text-center py-4">
                    <i class="fa fa-trash-alt text-danger fa-3x mb-3"></i>
                    <h5 class="fw-bold">Are you sure?</h5>
                    <p class="text-muted">You are about to delete this permission. This action cannot be undone.</p>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                        <i class="fa fa-times me-1"></i>Cancel
                    </button>
                    <button type="button" class="btn btn-danger" wire:click="deletePermission" data-bs-dismiss="modal">
                        <i class="fa fa-trash me-1"></i>Yes, Delete
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

@push('script')
    <script>
        // ✅ معالجة الرسائل من Livewire
        Livewire.on('show-message', (event) => {
            // إزالة أي تنبيهات سابقة
            const existingAlerts = document.querySelectorAll('.alert');
            existingAlerts.forEach(alert => alert.remove());

            // إنشاء التنبيه الجديد
            const alertDiv = document.createElement('div');
            alertDiv.className = `alert alert-${event.type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3 shadow-sm`;
            alertDiv.style.zIndex = '9999';
            alertDiv.innerHTML = `
                <div class="d-flex align-items-center">
                    <i class="fa ${event.type === 'success' ? 'fa-check-circle' : 'fa-exclamation-circle'} me-2"></i>
                    <span>${event.message}</span>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            `;

            document.body.appendChild(alertDiv);

            // إزالة التنبيه تلقائياً بعد 5 ثواني
            setTimeout(() => {
                if (alertDiv.parentElement) {
                    alertDiv.remove();
                }
            }, 5000);
        });
    </script>
@endpush
