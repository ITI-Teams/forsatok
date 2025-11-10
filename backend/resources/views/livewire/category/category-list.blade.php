<div class="container" data-bs-theme="auto">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold text-body">Categories</h1>
        <a href="{{ route('categories.trash') }}" wire:navigate
           class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-trash"></i> View Trash
        </a>
    </div>

    <!-- Search + Create Button -->
    <div class="d-flex justify-content-between mb-3">
        <input type="text" wire:model.live="search"
               class="form-control w-50"
               placeholder="🔍 Search by category name...">

        <a href="{{ route('categories.create') }}" 
           class="btn btn-primary d-flex align-items-center gap-2">
            <i class="fa-solid fa-plus"></i> Create New Category
        </a>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">

        <div class="card-header bg-body-tertiary py-3">
            <h6 class="mb-0 text-secondary fw-semibold">All Categories</h6>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($categories as $category)
                        <tr>
                            <td class="px-4 py-3">{{ $category->name }}</td>
                            <td class="px-4 py-3 text-center">
                                <a wire:navigate href="{{ route('categories.edit', $category->id) }}"
                                   class="btn btn-sm btn-primary rounded-3 px-3">
                                    <i class="fa-solid fa-pen-to-square"></i> Edit
                                </a>

                                <button onclick="confirmDelete({{ $category->id }})"
                                        class="btn btn-sm btn-danger rounded-3 px-3 ms-2">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center py-4 text-secondary">
                                No categories found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This category will be moved to trash.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('delete', id);
            }
        });
    }
</script>
