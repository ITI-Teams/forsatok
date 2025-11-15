<div>
    <!-- Alerts -->
    @if (session()->has('message'))
        <div class="alert alert-success fade show align-items-center" role="alert" id="successAlert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-trash me-2 text-danger"></i> Trashed Jobs
        </h4>

        <a wire:navigate href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Back to Jobs
        </a>
    </div>

    <!-- Delete Confirmation Alert -->
    @if ($confirmingDelete && $selectedJobId)
        <div class="alert alert-danger d-flex justify-content-between align-items-center mb-4">
            <div>
                <i class="fa-solid fa-triangle-exclamation me-2"></i>
                Are you sure you want to permanently delete this job? This action cannot be undone!
            </div>
            <div>
                <button wire:click="forceDelete" class="btn btn-sm btn-danger me-2">Yes, Delete Forever</button>
                <button wire:click="cancelForceDelete" class="btn btn-sm btn-secondary">Cancel</button>
            </div>
        </div>
    @endif

    <!-- Table -->
    <div class="card shadow-sm border-0" style="background-color: var(--bs-body-bg); color: var(--bs-body-color);">
        <div class="card-body p-0">
            @if ($trashedJobs->isEmpty())
                <div class="d-flex flex-column justify-content-center align-items-center py-5" style="color: var(--bs-secondary-color); min-height: 300px;">
                    <i class="fa-solid fa-inbox fa-3x mb-3" style="opacity: 0.5;"></i>
                    <p class="mb-0">No trashed jobs found.</p>
                </div>
            @else
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
                                <th>Deleted At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trashedJobs as $job)
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
                                            </div>
                                        @else
                                            <span style="color: var(--bs-secondary-color);">N/A</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="small">
                                            {{ $job->deleted_at->format('M d, Y') }}
                                            <br>
                                            <span style="color: var(--bs-secondary-color);">
                                                {{ $job->deleted_at->format('h:i A') }}
                                            </span>
                                        </div>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group" role="group">
                                            <button wire:click="restore({{ $job->id }})"
                                                    class="btn btn-sm btn-success"
                                                    title="Restore">
                                                <i class="fa-solid fa-rotate-left"></i>
                                            </button>
                                            <button wire:click="confirmForceDelete({{ $job->id }})"
                                                    class="btn btn-sm btn-danger"
                                                    title="Delete Permanently">
                                                <i class="fa-solid fa-trash-can"></i>
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
