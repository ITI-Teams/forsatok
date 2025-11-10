<div>
    <!-- Alerts -->
    @if (session()->has('message'))
        <div class="alert alert-success fade show align-items-center" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Delete confirmation -->
    @if ($confirmingDelete && $selectedJobId)
        <div class="alert alert-danger d-flex justify-content-between align-items-center">
            <div>
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                Are you sure you want to delete this job? This action can be undone from the Trash.
            </div>
            <div>
                <button wire:click="delete" class="btn btn-sm btn-danger me-2">Yes, Delete</button>
                <button wire:click="cancelDelete" class="btn btn-sm btn-secondary">Cancel</button>
            </div>
        </div>
    @endif

    <!-- Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold mb-0">
                <i class="fa-solid fa-briefcase me-2 text-primary"></i> Jobs
            </h4>
            <a wire:navigate href="{{ route('jobs.trash') }}" class="text-decoration-none text-muted small">
                <i class="fa-solid fa-trash me-1"></i> View Trash
            </a>
        </div>

        <a wire:navigate href="{{ route('jobs.create') }}" class="btn btn-primary px-4">
            <i class="fa-solid fa-plus me-2"></i> Add New Job
        </a>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Employer</th>
                            <th>Type</th>
                            <th>Location</th>
                            <th>Salary</th>
                            <th>Experience</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                            <tr>
                                <td>{{ $job->title }}</td>
                                <td>{{ $job->category->name ?? 'N/A' }}</td>
                                <td>{{ $job->employer->name ?? 'N/A' }}</td>
                                <td>{{ ucfirst($job->type) }}</td>
                                <td>{{ $job->location ?? 'N/A' }}</td>
                                <td>
                                    @if ($job->salary_min && $job->salary_max)
                                        ${{ number_format($job->salary_min) }} - ${{ number_format($job->salary_max) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $job->experince ?? 'N/A' }}</td>
                                <td>
                                    @if ($job->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <a wire:navigate href="{{ route('jobs.edit', $job->id) }}"
                                        class="btn btn-sm btn-warning me-2">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger"
                                        wire:click="confirmDelete({{ $job->id }})">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                    <a wire:navigate href="{{ route('jobs.show', $job->id) }}"
                                        class="btn btn-sm btn-info me-2 text-white">
                                        <i class="fa-solid fa-eye"></i>
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="9" class="text-center text-muted py-4">No jobs found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
