<div class="container" data-bs-theme="auto">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold text-body">Trashed Countries</h1>
        <a href="{{ route('countries.index') }}" wire:navigate
           class="btn btn-outline-primary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Back to Countries
        </a>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm d-flex align-items-center gap-2 mb-3">
            <i class="fa-solid fa-circle-check"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">

        <div class="card-header bg-body-tertiary py-3">
            <h6 class="mb-0 text-secondary fw-semibold">Deleted Countries</h6>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Code</th>
                        <th class="px-4 py-3">Deleted At</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trashedCountries as $country)
                        <tr>
                            <td class="px-4 py-3">{{ $country->name }}</td>
                            <td class="px-4 py-3">{{ $country->code ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $country->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="confirmRestore({{ $country->id }})"
                                        class="btn btn-sm btn-success rounded-3 px-3">
                                    <i class="fa-solid fa-rotate-left"></i> Restore
                                </button>

                                <button onclick="confirmForceDelete({{ $country->id }})"
                                        class="btn btn-sm btn-danger rounded-3 px-3 ms-2">
                                    <i class="fa-solid fa-trash"></i> Delete Permanently
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center py-4 text-secondary">
                                No trashed countries found.
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
    function confirmRestore(id) {
        Swal.fire({
            title: "Restore Country?",
            text: "This country will be restored.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, restore it!"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('restore', id);
            }
        });
    }

    function confirmForceDelete(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This country will be permanently deleted and cannot be recovered!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it permanently!"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('forceDelete', id);
            }
        });
    }
</script>

