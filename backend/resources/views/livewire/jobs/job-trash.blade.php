<div>
    <!-- ✅ Alerts -->
    @if (session()->has('message'))
        <div class="alert alert-success fade show align-items-center" role="alert" id="successAlert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('message') }}
        </div>
    @endif

    <!-- ✅ Header Bar -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="fw-semibold mb-0">
                <i class="fa-solid fa-trash me-2 text-danger"></i> Trashed Jobs
            </h4>
            <a wire:navigate href="{{ route('jobs.index') }}" class="text-decoration-none text-muted small">
                <i class="fa-solid fa-arrow-left me-1"></i> Back to Jobs
            </a>
        </div>
    </div>

    <!-- ✅ Table -->
    <div class="card shadow-sm border-0">
        <div class="card-body p-0">
            @if ($trashedJobs->isEmpty())
                <div class="text-center text-muted py-5">
                    <i class="fa-solid fa-inbox fa-2x mb-2"></i>
                    <p class="mb-0">No trashed jobs found.</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>Title</th>
                                <th>Category</th>
                                <th>Employer</th>
                                <th>Type</th>
                                <th>Deleted At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($trashedJobs as $job)
                                <tr>
                                    <td>{{ $job->title }}</td>
                                    <td>{{ $job->category->name ?? 'N/A' }}</td>
                                    <td>{{ $job->employer->name ?? 'N/A' }}</td>
                                    <td>{{ ucfirst($job->type) }}</td>
                                    <td>{{ $job->deleted_at->format('M d, Y h:i A') }}</td>
                                    <td class="text-center">
                                        <button wire:click="restore({{ $job->id }})"
                                                class="btn btn-sm btn-success me-2">
                                            <i class="fa-solid fa-rotate-left me-1"></i> Restore
                                        </button>
                                        <button wire:click="forceDelete({{ $job->id }})"
                                                class="btn btn-sm btn-danger">
                                            <i class="fa-solid fa-trash-can me-1"></i> Delete Permanently
                                        </button>
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
