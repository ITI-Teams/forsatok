<div class="container-fluid px-3 px-md-4 py-3 bg-body text-body">
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
    @if (session()->has('error'))
        <div class="alert alert-danger d-flex align-items-center fade show mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            {{ session('error') }}
        </div>
    @endif
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-trash-can me-2 text-danger"></i> Deleted Applications
        </h4>
        <a wire:navigate href="{{ route('job.app.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Back to Applications
        </a>
    </div>

    <div class="card shadow-sm border border-body bg-body text-body rounded-3">
        <div class="card-body p-0">
            @if($trashedApplications->isEmpty())
                <div class="text-center py-5">
                    <i class="fa-solid fa-trash-can text-secondary mb-3" style="font-size: 3rem;"></i>
                    <p class="text-secondary mb-0">No deleted applications found</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="border-bottom">
                            <tr>
                                <th scope="col" class="text-nowrap py-3">Candidate</th>
                                <th scope="col" class="text-nowrap">Job Post</th>
                                <th scope="col" class="text-nowrap">Status</th>
                                <th scope="col" class="text-nowrap">Deleted At</th>
                                <th scope="col" class="text-nowrap text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trashedApplications as $application)
                                <tr>
                                    <td class="text-nowrap">
                                        <div class="fw-bold">{{ $application->candidate->name ?? 'N/A' }}</div>
                                        <small class="text-muted">{{ $application->candidate->email ?? '' }}</small>
                                    </td>
                                    <td class="text-nowrap">
                                        <div class="fw-bold">{{ $application->jobPost->title ?? 'N/A' }}</div>
                                        <small class="text-muted">by {{ $application->jobPost->employer->name ?? 'N/A' }}</small>
                                    </td>
                                    <td class="text-nowrap">
                                        <span class="badge
                                            @if($application->status == 'pending') bg-warning
                                            @elseif($application->status == 'accepted') bg-success
                                            @elseif($application->status == 'rejected') bg-danger
                                            @else bg-secondary @endif">
                                            {{ ucfirst($application->status) }}
                                        </span>
                                    </td>
                                    <td class="text-nowrap">{{ $application->deleted_at->diffForHumans() }}</td>
                                    <td class="text-nowrap text-end">
                                        <div class="d-flex flex-wrap justify-content-end gap-2">
                                            <button type="button"
                                                    wire:click="restore({{ $application->id }})"
                                                    class="btn btn-sm btn-outline-success d-flex align-items-center"
                                                    wire:loading.attr="disabled"
                                                    wire:target="restore({{ $application->id }})">
                                                <span wire:loading.remove wire:target="restore({{ $application->id }})">
                                                    <i class="fa-solid fa-trash-arrow-up me-1"></i> Restore
                                                </span>
                                                <span wire:loading wire:target="restore({{ $application->id }})">
                                                    <i class="fa-solid fa-spinner fa-spin me-1"></i>
                                                </span>
                                            </button>
                                            <button type="button"
                                                    onclick="confirmForceDelete({{ $application->id }})"
                                                    class="btn btn-sm btn-outline-danger d-flex align-items-center"
                                                    >
                                                <i class="fa-solid fa-trash me-1"></i>
                                                Delete
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @endif
        </div>
    </div>
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
