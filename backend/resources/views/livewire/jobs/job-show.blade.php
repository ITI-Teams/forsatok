<div class="container py-4">
    <!-- Back link -->
    <a wire:navigate href="{{ route('jobs.index') }}" class="text-muted mb-3 d-inline-block">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Jobs
    </a>

    <!-- Job Card -->
    <div class="card shadow-sm border-0">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h3 class="fw-bold mb-1">{{ $job->title }}</h3>
                    <p class="text-muted mb-0">
                        <i class="fa-solid fa-briefcase me-1"></i> {{ ucfirst($job->type) }} |
                        <i class="fa-solid fa-location-dot me-1"></i> {{ $job->location ?? 'Remote' }}
                    </p>
                </div>

                @if($job->is_active)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Inactive</span>
                @endif
            </div>

            <!-- Info -->
            <div class="row mb-4">
                <div class="col-md-4">
                    <strong>Category:</strong>
                    <p>{{ $job->category->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Employer:</strong>
                    <p>{{ $job->employer->name ?? 'N/A' }}</p>
                </div>
                <div class="col-md-4">
                    <strong>Experience:</strong>
                    <p>{{ $job->experince ?? 'N/A' }}</p>
                </div>
            </div>

            <!-- Salary -->
            <div class="mb-4">
                <strong>Salary Range:</strong><br>
                @if($job->salary_min && $job->salary_max)
                    ${{ number_format($job->salary_min) }} - ${{ number_format($job->salary_max) }}
                @else
                    <span class="text-muted">Not specified</span>
                @endif
            </div>

            <!-- Description -->
            <div class="mb-4">
                <strong>Job Description:</strong>
                <div class="mt-2">
                    {!! nl2br(e($job->description)) !!}
                </div>
            </div>

            <!--  Deadline -->
            <div>
                <strong>Application Deadline:</strong><br>
                @if($job->deadline)
                    <span class="text-danger">
                        {{ \Carbon\Carbon::parse($job->deadline)->format('M d, Y h:i A') }}
                    </span>
                @else
                    <span class="text-muted">No deadline specified</span>
                @endif
            </div>
        </div>
    </div>
</div>
