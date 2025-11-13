<div class="container-fluid px-3 px-md-4 py-3 bg-body text-body">
    @if (session()->has('message'))
        <div class="alert alert-success d-flex align-items-center fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('message') }}
        </div>
    @endif
    @if(session('error'))
        <div class="alert alert-danger alert-dismissible fade show mb-4 d-flex align-items-center" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            <div>{{ session('error') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <!-- Header + Search + Buttons -->
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-file-lines me-2 text-primary"></i> Applications List
        </h4>
        <div class="d-flex flex-wrap gap-2">
            <livewire:search.search :search-fields="['candidate.name', 'jobPost.title']"
                emit-event="applicationSearchUpdated" placeholder="Search applications..." />

            <a wire:navigate href="{{ route('job.app.create') }}" class="btn btn-primary px-4">
                <i class="fa-solid fa-plus me-2"></i> New Application
            </a>
            <a wire:navigate href="{{ route('job.app.trash') }}" class="btn btn-outline-secondary px-4">
                <i class="fa-solid fa-trash me-2"></i> Trashed Applications
            </a>
        </div>
    </div>

    <!-- Applications Table -->
    <div class="card shadow-sm border border-body bg-body text-body rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="border-bottom">
                            <th>Candidate Name</th>
                            <th>Job Post</th>
                            <th>Status</th>
                            <th>Applied Date</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($applications as $application)
                            <tr>
                                <td>
                                    <div class="fw-bold">{{ $application->candidate->name ?? 'N/A' }}</div>
                                    <small class="text-muted">{{ $application->candidate->email ?? '' }}</small>
                                </td>
                                <td>
                                    <div class="fw-bold">{{ $application->jobPost->title ?? 'N/A' }}</div>
                                    <small class="text-muted">by {{ $application->jobPost->employer->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge
                                        @if($application->status == 'pending') bg-warning
                                        @elseif($application->status == 'accepted') bg-success
                                        @elseif($application->status == 'rejected') bg-danger
                                        @else bg-secondary @endif">
                                        {{ ucfirst($application->status) }}
                                    </span>
                                </td>
                                <td>{{ $application->created_at->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <a wire:navigate href="{{ route('job.app.show', $application->id) }}"
                                        class="btn btn-sm btn-info me-2">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                    <a wire:navigate href="{{ route('job.app.edit', $application->id) }}"
                                        class="btn btn-sm btn-warning me-2">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button onclick="confirmDelete({{ $application->id }})"
                                        class="btn btn-sm btn-danger me-2">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    @if($application->resume_path)
                                        <a href="{{ asset('storage/' . $application->resume_path) }}" target="_blank"
                                            class="btn btn-sm btn-success">
                                            <i class="fa-solid fa-download"></i>
                                        </a>
                                    @endif
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No applications found.
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
