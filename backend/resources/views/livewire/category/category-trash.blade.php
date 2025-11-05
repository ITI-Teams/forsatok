<div class="container">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold text-dark">🗑️ Trash - Deleted Categories</h1>

        <a href="{{ route('categories.index') }}" wire:navigate
           class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Back to Categories
        </a>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    <!-- Table Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-light py-3">
            <h6 class="mb-0 text-secondary fw-semibold">Trashed Categories</h6>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0">
                <thead class="bg-light text-secondary text-uppercase text-sm">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Deleted At</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trashedCategories as $category)
                        <tr>
                            <td class="px-4 py-3">{{ $category->name }}</td>
                            <td class="px-4 py-3">{{ $category->deleted_at->diffForHumans() }}</td>
                            <td class="px-4 py-3 text-center">
                                <button wire:click="restore({{ $category->id }})"
                                        class="btn btn-sm btn-success rounded-3">
                                    <i class="fa-solid fa-rotate-left"></i> Restore
                                </button>

                                <button onclick="confirmForceDelete({{ $category->id }})"
                                        class="btn btn-sm btn-danger rounded-3 ms-2">
                                    <i class="fa-solid fa-times"></i> Delete Permanently
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center py-4 text-muted">
                                No deleted categories found.
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
    function confirmForceDelete(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This action will permanently delete the category!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete permanently"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('forceDelete', id);
            }
        });
    }
</script>
