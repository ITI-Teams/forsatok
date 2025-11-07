<div class="container" data-bs-theme="auto">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold text-body">🗑️ Trash - Deleted Applications</h1>

        <a href="{{ route('job.app.index') }}" wire:navigate
           class="btn btn-outline-secondary d-flex align-items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Back to Applications
        </a>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-check"></i>
            <span>{{ session('message') }}</span>
        </div>
    @endif

    @if (session()->has('error'))
        <div class="alert alert-danger border-0 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-exclamation"></i>
            <span>{{ session('error') }}</span>
        </div>
    @endif

    <!-- Table Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">

        <div class="card-header bg-body-tertiary py-3">
            <h6 class="mb-0 text-secondary fw-semibold">Trashed Applications</h6>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-body-tertiary text-secondary text-uppercase text-sm">
                <tr>
                    <th class="px-4 py-3">Candidate</th>
                    <th class="px-4 py-3">Job Post</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Deleted At</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($trashedApplications as $application)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="fw-semibold">{{ $application->candidate->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $application->candidate->email ?? '' }}</small>
                        </td>
                        <td class="px-4 py-3">
                            <div class="fw-semibold">{{ $application->jobPost->title ?? 'N/A' }}</div>
                            <small class="text-muted">by {{ $application->jobPost->employer->name ?? 'N/A' }}</small>
                        </td>
                        <td class="px-4 py-3">
                                <span class="badge
                                    @if($application->status == 'pending') bg-warning
                                    @elseif($application->status == 'accepted') bg-success
                                    @elseif($application->status == 'rejected') bg-danger
                                    @else bg-secondary @endif">
                                    {{ ucfirst($application->status) }}
                                </span>
                        </td>
                        <td class="px-4 py-3">
                            <div class="text-danger">
                                <i class="fa-solid fa-clock me-1"></i>
                                {{ $application->deleted_at->diffForHumans() }}
                            </div>
                            <small class="text-muted">
                                {{ $application->deleted_at->format('M d, Y \\a\\t h:i A') }}
                            </small>
                        </td>
                        <td class="px-4 py-3 text-center">
                            <button wire:click="restore({{ $application->id }})"
                                    class="btn btn-sm btn-success rounded-3 px-3"
                                    wire:loading.attr="disabled"
                                    wire:target="restore({{ $application->id }})">
                                    <span wire:loading.remove wire:target="restore({{ $application->id }})">
                                        <i class="fa-solid fa-rotate-left"></i> Restore
                                    </span>
                                <span wire:loading wire:target="restore({{ $application->id }})">
                                        <i class="fa-solid fa-spinner fa-spin"></i>
                                    </span>
                            </button>

                            <button onclick="confirmForceDelete({{ $application->id }})"
                                    class="btn btn-sm btn-danger rounded-3 px-3 ms-2"
                                    wire:loading.attr="disabled">
                                <i class="fa-solid fa-times"></i> Delete Permanently
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-5 text-secondary">
                            <div class="py-4">
                                <i class="fa-solid fa-trash-can fa-2x mb-3 text-muted"></i>
                                <h6 class="text-muted">No deleted applications found</h6>
                                <small class="text-muted">Applications you delete will appear here</small>
                            </div>
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Bulk Actions -->
    @if(count($trashedApplications) > 0)
        <div class="card shadow-sm border-0 rounded-4 mt-4">
            <div class="card-body">
                <div class="d-flex justify-content-between align-items-center">
                    <small class="text-muted">
                        Showing {{ count($trashedApplications) }} deleted application(s)
                    </small>
                    <div>
                        <button onclick="confirmEmptyTrash()"
                                class="btn btn-outline-danger btn-sm">
                            <i class="fa-solid fa-broom"></i> Empty Trash
                        </button>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- SweetAlert Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmForceDelete(id) {
        Swal.fire({
            title: "Delete Permanently?",
            text: "This action cannot be undone. The application will be permanently deleted!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete permanently",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
            @this.call('forceDelete', id);
            }
        });
    }

    function confirmEmptyTrash() {
        Swal.fire({
            title: "Empty Trash?",
            text: "This will permanently delete all applications in the trash. This action cannot be undone!",
            icon: "error",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, empty trash",
            cancelButtonText: "Cancel"
        }).then((result) => {
            if (result.isConfirmed) {
            @this.call('emptyTrash');
            }
        });
    }

    @if (session()->has('message'))
    Swal.fire({
        title: "Success!",
        text: "{{ session('message') }}",
        icon: "success",
        confirmButtonColor: "#28a745",
        timer: 3000
    });
    @endif

    @if (session()->has('error'))
    Swal.fire({
        title: "Error!",
        text: "{{ session('error') }}",
        icon: "error",
        confirmButtonColor: "#dc3545"
    });
    @endif
</script>
