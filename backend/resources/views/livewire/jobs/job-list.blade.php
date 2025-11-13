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
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-briefcase me-2 text-primary"></i> Jobs
        </h4>

        <div class="d-flex gap-2">
            <a wire:navigate href="{{ route('jobs.trash') }}" class="btn btn-outline-danger">
                <i class="fa-solid fa-trash me-2"></i> View Trash
            </a>
            <a wire:navigate href="{{ route('jobs.create') }}" class="btn btn-primary px-4">
                <i class="fa-solid fa-plus me-2"></i> Add New Job
            </a>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border-0" style="background-color: var(--bs-body-bg); color: var(--bs-body-color);">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table align-middle mb-0 table-hover">
                    <thead style="background-color: var(--bs-tertiary-bg); color: var(--bs-body-color);">
                        <tr>
                            <th>Title</th>
                            <th>Category</th>
                            <th>Employer</th>
                            <th>Work Type</th>
                            <th>Work Place</th>
                            <th>Location</th>
                            <th>Salary</th>
                            <th>Experience</th>
                            <th>Deadline</th>
                            <th>Status</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($jobs as $job)
                            <tr style="border-color: var(--bs-border-color);">
                                <td class="fw-semibold">{{ $job->title }}</td>
                                <td>
                                    <span class="badge bg-primary">{{ $job->category->name ?? 'N/A' }}</span>
                                </td>
                                <td>{{ $job->employer->name ?? 'N/A' }}</td>
                                <td>
                                    <span class="badge bg-info text-white">
                                        {{ ucfirst($job->work_type ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>
                                    <span class="badge bg-secondary">
                                        {{ ucfirst($job->work_place ?? 'N/A') }}
                                    </span>
                                </td>
                                <td>
                                    @if ($job->location)
                                        <div class="small">
                                            @if ($job->location->city && $job->location->country)
                                                {{ $job->location->city->name }}, {{ $job->location->country->name }}
                                            @elseif ($job->location->country)
                                                {{ $job->location->country->name }}
                                            @else
                                                N/A
                                            @endif
                                            @if ($job->location->address)
                                                <br>
                                                <span style="color: var(--bs-secondary-color);">{{ $job->location->address }}</span>
                                            @endif
                                        </div>
                                    @else
                                        <span style="color: var(--bs-secondary-color);">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($job->salary_min && $job->salary_max)
                                        <div class="small">
                                            ${{ number_format($job->salary_min) }} - ${{ number_format($job->salary_max) }}
                                        </div>
                                    @else
                                        <span style="color: var(--bs-secondary-color);">N/A</span>
                                    @endif
                                </td>
                                <td>{{ $job->experience ?? 'N/A' }}</td>
                                <td>
                                    @if ($job->deadline)
                                        <div class="small">
                                            {{ \Carbon\Carbon::parse($job->deadline)->format('M d, Y') }}
                                        </div>
                                    @else
                                        <span style="color: var(--bs-secondary-color);">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($job->is_active)
                                        <span class="badge bg-success">Active</span>
                                    @else
                                        <span class="badge bg-secondary">Inactive</span>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <div class="btn-group" role="group">
                                        <a wire:navigate href="{{ route('jobs.show', $job->id) }}"
                                            class="btn btn-sm btn-info text-white" title="View">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                        <a wire:navigate href="{{ route('jobs.edit', $job->id) }}"
                                            class="btn btn-sm btn-warning" title="Edit">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </a>
                                        <button class="btn btn-sm btn-danger"
                                            wire:click="confirmDelete({{ $job->id }})" title="Delete">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center py-4" style="color: var(--bs-secondary-color);">
                                    <i class="fa-solid fa-inbox fa-3x mb-3 d-block" style="opacity: 0.5;"></i>
                                    No jobs found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
