<div class="container" data-bs-theme="auto">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold text-body">Applications</h1>
        <a href="{{ route('job.app.trash') }}" wire:navigate
           class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-trash"></i> View Trash
        </a>
    </div>

    <!-- Search + Create Button -->
    <div class="d-flex justify-content-between mb-3">
        <input type="text" wire:model.live="search"
               class="form-control w-50"
               placeholder="🔍 Search by application name...">

        <a wire:navigate href="{{ route('job.app.create') }}"
           class="btn btn-primary d-flex align-items-center gap-2">
            <i class="fa-solid fa-plus"></i> Create New Application
        </a>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mt-3" role="alert">
            {{ session('error') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Table Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">

        <div class="card-header bg-body-tertiary py-3">
            <h6 class="mb-0 text-secondary fw-semibold">All Applications</h6>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-body-tertiary">
                <tr>
                    <th class="px-4 py-3">Candidate Name</th>
                    <th class="px-4 py-3">Job Post</th>
                    <th class="px-4 py-3">Status</th>
                    <th class="px-4 py-3">Applied Date</th>
                    <th class="px-4 py-3 text-center">Actions</th>
                </tr>
                </thead>
                <tbody>
                @forelse($applications as $application)
                    <tr>
                        <td class="px-4 py-3">
                            <div class="fw-bold">{{ $application->candidate->name ?? 'N/A' }}</div>
                            <small class="text-muted">{{ $application->candidate->email ?? '' }}</small>
                        </td>
                        <td class="px-4 py-3">
                            <div class="fw-bold">{{ $application->jobPost->title ?? 'N/A' }}</div>
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
                            {{ $application->created_at->format('M d, Y') }}
                        </td>
                        <td class="px-4 py-3 text-center">
                            <a wire:navigate href="{{ route('job.app.show', $application->id) }}"
                               class="btn btn-sm btn-info rounded-3 px-3"
                               title="View Application Details">
                                <i class="fa-solid fa-eye"></i> View Details
                            </a>

                            <a wire:navigate href="{{ route('job.app.edit', $application->id) }}"
                               class="btn btn-sm btn-primary rounded-3 px-3 ms-1"
                               title="Edit Application">
                                <i class="fa-solid fa-pen-to-square"></i> Edit
                            </a>

                            <button onclick="confirmDelete({{ $application->id }})"
                                    class="btn btn-sm btn-danger rounded-3 px-3 ms-1"
                                    title="Delete Application">
                                <i class="fa-solid fa-trash"></i> Delete
                            </button>

                            @if($application->resume_path)
                                <a wire:navigate href="{{ asset('storage/' . $application->resume_path) }}"
                                   target="_blank"
                                   class="btn btn-sm btn-success rounded-3 px-3 ms-1"
                                   title="Download Resume">
                                    <i class="fa-solid fa-download"></i> Resume
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center py-4 text-secondary">
                            <i class="fa-solid fa-inbox fa-2x mb-2"></i>
                            <br>
                            No Applications found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
    <div class="mt-4 px-3">
        {{ $applications->links() }}
    </div>
</div>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This Application will be moved to trash.",
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
