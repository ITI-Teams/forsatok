<div>
    <h3 class="mb-3 fw-bold text-primary">Permissions Management</h3>

    <!-- ✅ تنبيه ثابت في الأعلى -->
    <div class="position-fixed top-0 start-50 translate-middle-x mt-3" style="z-index: 9999;">
        @if (session()->has('message'))
            <div class="alert alert-success alert-dismissible fade show shadow-sm" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fa fa-check-circle me-2"></i>
                    <span>{{ session('message') }}</span>
                    <button type="button" class="btn-close ms-2" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            </div>
        @endif
    </div>

    <!-- ✅ نموذج الإضافة/التعديل -->
    <form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}" class="card p-3 border-0 shadow-sm mb-4 bg-body-tertiary">
        <div class="mb-3">
            <label class="form-label fw-semibold">Permission Name</label>
            <input type="text" wire:model="name" placeholder="Enter permission name"
                   class="form-control @error('name') is-invalid @enderror">
            @error('name')
            <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="d-flex gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="fa {{ $updateMode ? 'fa-save' : 'fa-plus' }} me-1"></i>
                {{ $updateMode ? 'Update Permission' : 'Create Permission' }}
            </button>
            @if($updateMode)
                <button type="button" wire:click="resetInput" class="btn btn-secondary">
                    <i class="fa fa-times me-1"></i>Cancel
                </button>
            @endif
        </div>
    </form>

    <!-- ✅ جدول الصلاحيات -->
    <div class="card border-0 shadow-sm">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="table-light">
                    <tr>
                        <th class="border-0">#</th>
                        <th class="border-0">Permission Name</th>
                        <th class="border-0 text-center">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse ($permissions as $permission)
                        <tr>
                            <td>{{ $permission->id }}</td>
                            <td>
                                    <span class="badge bg-primary bg-opacity-10 text-primary fs-6">
                                        {{ ucfirst($permission->name) }}
                                    </span>
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-warning" wire:click="edit({{ $permission->id }})">
                                    <i class="fa fa-edit me-1"></i>Edit
                                </button>
                                <button class="btn btn-sm btn-danger"
                                        wire:click="confirmDelete({{ $permission->id }})"
                                        data-bs-toggle="modal"
                                        data-bs-target="#deleteModal">
                                    <i class="fa fa-trash me-1"></i>Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-muted py-4">
                                <i class="fa fa-inbox fa-2x mb-2"></i>
                                <br>
                                No permissions found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- ✅ الترقيم -->
    <div class="mt-3">
        {{ $permissions->links() }}
    </div>

    <!-- ✅ مودال التأكيد على الحذف -->
    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content border-0 shadow">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">
                        <i class="fa fa-exclamation-triangle me-2"></i>Confirm Delete
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
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
