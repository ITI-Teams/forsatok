<div class="container-fluid px-3 px-md-4 py-3 bg-body text-body">

    <!-- Alerts -->
    @if (session()->has('message'))
        <div class="alert alert-success d-flex align-items-center fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Header + Search + Buttons -->
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-briefcase me-2 text-primary"></i> Jobs
        </h4>

        <div class="d-flex flex-wrap gap-2">
            <livewire:search.search :search-fields="['title','experience']" emit-event="jobSearchUpdated"
                placeholder="Search jobs..." />
            @can('jobs.manage')
                <a wire:navigate href="{{ route('jobs.create') }}" class="btn btn-primary px-4">
                    <i class="fa-solid fa-plus me-2"></i> Add New Job
                </a>
                <a wire:navigate href="{{ route('jobs.trash') }}" class="btn btn-outline-secondary px-4">
                    <i class="fa-solid fa-trash me-2"></i> View Trash
                </a>
            @endcan

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
                            <tr>
                                <td>{{ $job->title }}</td>
                                <td><span class="badge bg-primary">{{ $job->category->name ?? 'N/A' }}</span></td>
                                <td>{{ $job->employer->name ?? 'N/A' }}</td>
                                <td><span class="badge bg-info text-white">{{ ucfirst($job->work_type ?? 'N/A') }}</span>
                                </td>
                                <td><span class="badge bg-secondary">{{ ucfirst($job->work_place ?? 'N/A') }}</span></td>
                                <td>
                                    @if($job->location)
                                        @if($job->location->city && $job->location->country)
                                            {{ $job->location->city->name }}, {{ $job->location->country->name }}
                                        @elseif($job->location->country)
                                            {{ $job->location->country->name }}
                                        @else
                                            N/A
                                        @endif
                                        @if($job->location->address)
                                            <br><small
                                                style="color: var(--bs-secondary-color)">{{ $job->location->address }}</small>
                                        @endif
                                    @else
                                        <span style="color: var(--bs-secondary-color)">N/A</span>
                                    @endif
                                </td>
                                <td>
                                    @if ($job->salary_min && $job->salary_max)
                                        ${{ number_format($job->salary_min) }} - ${{ number_format($job->salary_max) }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>{{ $job->experience ?? 'N/A' }}</td>
                                <td>
                                    @if($job->deadline)
                                        {{ \Carbon\Carbon::parse($job->deadline)->format('M d, Y') }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>
                                    <div class="d-flex flex-column gap-1">
                                        @if($job->is_active)
                                            <span class="badge bg-success">Active</span>
                                        @else
                                            <span class="badge bg-secondary">Inactive</span>
                                        @endif

                                        @if($job->status === \App\Domains\Jobs\Models\JobPost::STATUS_APPROVED)
                                            <span class="badge bg-info text-white">Approved</span>
                                        @elseif($job->status === \App\Domains\Jobs\Models\JobPost::STATUS_REJECTED)
                                            <span class="badge bg-danger">Rejected</span>
                                        @elseif($job->status === \App\Domains\Jobs\Models\JobPost::STATUS_EXPIRED)
                                            <span class="badge bg-dark">Expired</span>
                                        @else
                                            <span class="badge bg-warning text-dark">Pending</span>
                                        @endif
                                    </div>
                                </td>
                                <td class="text-center">
                                    <div class="btn-group">
                                    @if(auth()->id() === $job->employer_id)
                                        @can('jobs.manage')
                                            <a wire:navigate href="{{ route('jobs.show', $job->id) }}"
                                                class="btn btn-sm btn-info text-white">
                                                <i class="fa-solid fa-eye"></i>
                                            </a>
                                            <a wire:navigate href="{{ route('jobs.edit', $job->id) }}"
                                                class="btn btn-sm btn-warning">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <button class="btn btn-sm btn-danger" onclick="confirmDelete({{ $job->id }})">
                                                <i class="fa-solid fa-trash"></i>
                                            </button>
                                        @endcan
                                    @else
                                        <a wire:navigate href="{{ route('jobs.show', $job->id) }}"
                                           class="btn btn-sm btn-info text-white">
                                            <i class="fa-solid fa-eye"></i>
                                        </a>
                                    @endif
                                        @can('jobs.approve')
                                            <button wire:click="openApprovalModal({{ $job->id }})"
                                                class="btn btn-sm btn-primary text-white" title="Manage Approval">
                                                <i class="fa-solid fa-stamp"></i>
                                            </button>
                                        @endcan
                                        @if(auth()->user()->type === 'employer' && in_array($job->status, [\App\Domains\Jobs\Models\JobPost::STATUS_REJECTED, \App\Domains\Jobs\Models\JobPost::STATUS_EXPIRED]))
                                            <button wire:click="resubmitJob({{ $job->id }})" class="btn btn-sm btn-outline-primary" title="Re-submit for Approval">
                                                <i class="fa-solid fa-rotate-right"></i>
                                            </button>
                                        @endif
                                    </div>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="11" class="text-center text-muted py-4">
                                    No jobs found.
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
        {{ $jobs->links() }}
    </div>
    @if($showApprovalModal && $selectedJob)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Job Approval Management</h5>
                        <button type="button" class="btn-close" wire:click="closeApprovalModal"></button>
                    </div>
                    <div class="modal-body">
                        <p>Managing approval for: <strong>{{ $selectedJob->title }}</strong></p>
                        <p>Current Status: <span class="badge bg-info">{{ ucfirst($selectedJob->status) }}</span></p>

                        <div class="mb-3">
                            <label class="form-label">Rejection Reason (Required if rejecting)</label>
                            <textarea wire:model="rejectionReason" class="form-control @error('rejectionReason') is-invalid @enderror" rows="3"
                                placeholder="Explain why the job is rejected..."></textarea>
                            @error('rejectionReason') <span class="text-danger small">{{ $message }}</span> @enderror
                        </div>
                    </div>
                    <div class="modal-footer justify-content-between">
                        <div>
                            <button wire:click="approveJob({{ $selectedJob->id }})" class="btn btn-success">
                                <i class="fa-solid fa-check me-1"></i> Approve
                            </button>
                            <button wire:click="rejectJob({{ $selectedJob->id }})" class="btn btn-danger">
                                <i class="fa-solid fa-xmark me-1"></i> Reject
                            </button>
                        </div>
                        <button wire:click="closeApprovalModal" class="btn btn-secondary">Cancel</button>
                    </div>
                </div>
            </div>
        </div>
    @endif

</div>

<!-- SweetAlert -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This job will be moved to trash.",
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
<script>
    window.addEventListener('toast', event => {
        const type = event.detail.type || 'info';
        const message = event.detail.message || 'Update Job Status';
        Swal.fire({
            toast: true,
            position: 'top-end',
            icon: type,
            title: message,
            showConfirmButton: false,
            timer: 3000,
            timerProgressBar: true
        });
    });
</script>