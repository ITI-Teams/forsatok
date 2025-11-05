<div>
    <h3 class="mb-3 fw-bold text-primary">Roles Management</h3>

    <form wire:submit.prevent="{{ $updateMode ? 'update' : 'store' }}">
        <div class="mb-3">
            <input type="text" wire:model="name" placeholder="Role name" class="form-control">
            @error('name') <span class="text-danger">{{ $message }}</span> @enderror
        </div>
        <button class="btn btn-primary">{{ $updateMode ? 'Update' : 'Create' }}</button>
    </form>

    <hr>

    <table class="table table-hover mt-3 align-middle">
        <thead class="table-light">
        <tr>
            <th>#</th>
            <th>Role Name</th>
            <th class="text-center">Actions</th>
        </tr>
        </thead>
        <tbody>
        @foreach ($roles as $role)
            <tr>
                <td>{{ $role->id }}</td>
                <td>{{ ucfirst($role->name) }}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-warning" wire:click="edit({{ $role->id }})">
                        <i class="fa fa-edit me-1"></i>Edit
                    </button>
                    <button class="btn btn-sm btn-danger" wire:click="confirmDelete({{ $role->id }})" data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="fa fa-trash me-1"></i>Delete
                    </button>
                </td>
            </tr>
        @endforeach
        </tbody>
    </table>

    <div class="mt-3">
        {{ $roles->links() }}
    </div>

    {{-- ✅ Modal تأكيد الحذف --}}
    <div wire:ignore.self class="modal fade" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content border-0 shadow-sm">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteModalLabel">Confirm Delete</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    Are you sure you want to delete this role?
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancel</button>
                    <button wire:click="deleteRole" type="button" class="btn btn-danger" data-bs-dismiss="modal">Yes, Delete</button>
                </div>
            </div>
        </div>
    </div>

</div>
