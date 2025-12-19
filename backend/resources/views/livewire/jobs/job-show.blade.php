<div class="container py-4">
    <!-- Back link -->
    <a wire:navigate href="{{ route('jobs.index') }}" class="mb-3 d-inline-block"
       style="color: var(--bs-secondary-color); text-decoration: none;">
        <i class="fa-solid fa-arrow-left me-1"></i> Back to Jobs
    </a>

    <!-- Job Card -->
    <div class="card shadow-sm border-0" style="background-color: var(--bs-body-bg); color: var(--bs-body-color);">
        <div class="card-body">
            <div class="d-flex justify-content-between align-items-start mb-3">
                <div>
                    <h3 class="fw-bold mb-1">{{ $job->title }}</h3>
                    <p class="mb-0" style="color: var(--bs-secondary-color);">
                        <i class="fa-solid fa-briefcase me-1"></i> {{ ucfirst($job->work_type ?? 'N/A') }} |
                        <i class="fa-solid fa-location-dot me-1"></i>
                        @if ($job->location)
                            @if ($job->location->city && $job->location->country)
                                {{ $job->location->city->name }}, {{ $job->location->country->name }}
                            @elseif ($job->location->country)
                                {{ $job->location->country->name }}
                            @else
                                N/A
                            @endif
                        @else
                            N/A
                        @endif
                        |
                        <i class="fa-solid fa-building me-1"></i> {{ ucfirst($job->work_place ?? 'N/A') }}
                    </p>
                </div>

                @if($job->is_active)
                    <span class="badge bg-success fs-6">Active</span>
                @else
                    <span class="badge bg-secondary fs-6">Inactive</span>
                @endif
            </div>

            <!-- Info Grid -->
            <div class="row mb-4">
                <div class="col-md-3 mb-3">
                    <div class="p-3 rounded" style="background-color: var(--bs-tertiary-bg);">
                        <small class="d-block mb-1" style="color: var(--bs-secondary-color);">
                            <i class="fa-solid fa-tag me-1"></i> Category
                        </small>
                        <strong>{{ $job->category->name ?? 'N/A' }}</strong>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="p-3 rounded" style="background-color: var(--bs-tertiary-bg);">
                        <small class="d-block mb-1" style="color: var(--bs-secondary-color);">
                            <i class="fa-solid fa-user-tie me-1"></i> Employer
                        </small>
                        <strong>{{ $job->employer->name ?? 'N/A' }}</strong>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="p-3 rounded" style="background-color: var(--bs-tertiary-bg);">
                        <small class="d-block mb-1" style="color: var(--bs-secondary-color);">
                            <i class="fa-solid fa-clock me-1"></i> Experience
                        </small>
                        <strong>{{ $job->experience ?? 'N/A' }}</strong>
                    </div>
                </div>
                <div class="col-md-3 mb-3">
                    <div class="p-3 rounded" style="background-color: var(--bs-tertiary-bg);">
                        <small class="d-block mb-1" style="color: var(--bs-secondary-color);">
                            <i class="fa-solid fa-briefcase-clock me-1"></i> Work Type
                        </small>
                        <strong>{{ ucfirst($job->work_type ?? 'N/A') }}</strong>
                    </div>
                </div>
            </div>

            <!-- Location Section -->
            @if ($job->location)
                <div class="mb-4 p-3 rounded" style="background-color: var(--bs-tertiary-bg);">
                    <h5 class="fw-semibold mb-3">
                        <i class="fa-solid fa-map-marker-alt text-danger me-2"></i> Location
                    </h5>
                    <div class="row">
                        <div class="col-md-4 mb-2">
                            <small class="d-block" style="color: var(--bs-secondary-color);">Country</small>
                            <strong>{{ $job->location->country->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="d-block" style="color: var(--bs-secondary-color);">City</small>
                            <strong>{{ $job->location->city->name ?? 'N/A' }}</strong>
                        </div>
                        <div class="col-md-4 mb-2">
                            <small class="d-block" style="color: var(--bs-secondary-color);">Address</small>
                            <strong>{{ $job->location->address ?? 'N/A' }}</strong>
                        </div>
                    </div>
                </div>
            @endif

            <!-- Salary -->
            <div class="mb-4 p-3 rounded" style="background-color: var(--bs-tertiary-bg);">
                <h5 class="fw-semibold mb-2">
                    <i class="fa-solid fa-dollar-sign text-success me-2"></i> Salary Range
                </h5>
                @if($job->salary_min && $job->salary_max)
                    <strong class="fs-5">${{ number_format($job->salary_min) }} - ${{ number_format($job->salary_max) }}</strong>
                @else
                    <span style="color: var(--bs-secondary-color);">Not specified</span>
                @endif
            </div>

            <!-- Description -->
            <div class="mb-4">
                <h5 class="fw-semibold mb-3">
                    <i class="fa-solid fa-file-lines text-primary me-2"></i> Job Description
                </h5>
                <div class="p-3 rounded" style="background-color: var(--bs-tertiary-bg);">
                    {!! nl2br(e($job->description)) !!}
                </div>
            </div>

            <!-- Responsibilities, Qualifications, Benefits -->
            <div class="row mb-4">
                <div class="col-md-4 mb-3">
                    <h5 class="fw-semibold mb-3">
                        <i class="fa-solid fa-list-check text-info me-2"></i> Responsibilities
                    </h5>
                    <div class="p-3 rounded" style="background-color: var(--bs-tertiary-bg);">
                        {!! nl2br(e($job->responsibilities ?? 'N/A')) !!}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="fw-semibold mb-3">
                        <i class="fa-solid fa-user-graduate text-warning me-2"></i> Qualifications
                    </h5>
                    <div class="p-3 rounded" style="background-color: var(--bs-tertiary-bg);">
                        {!! nl2br(e($job->qualification ?? 'N/A')) !!}
                    </div>
                </div>
                <div class="col-md-4 mb-3">
                    <h5 class="fw-semibold mb-3">
                        <i class="fa-solid fa-gift text-success me-2"></i> Benefits
                    </h5>
                    <div class="p-3 rounded" style="background-color: var(--bs-tertiary-bg);">
                        {!! nl2br(e($job->benefits ?? 'N/A')) !!}
                    </div>
                </div>
            </div>

            <!-- Deadline -->
            <div class="row">
                <div class="col-md-6 mb-4">
                     <div class="p-3 rounded h-100" style="background-color: var(--bs-tertiary-bg);">
                        <h5 class="fw-semibold mb-2">
                            <i class="fa-solid fa-calendar text-danger me-2"></i> Application Deadline
                        </h5>
                        @if($job->deadline)
                            <strong class="text-danger fs-5">
                                {{ \Carbon\Carbon::parse($job->deadline)->format('M d, Y h:i A') }}
                            </strong>
                            <small class="d-block mt-1" style="color: var(--bs-secondary-color);">
                                ({{ \Carbon\Carbon::parse($job->deadline)->diffForHumans() }})
                            </small>
                        @else
                            <span style="color: var(--bs-secondary-color);">No deadline specified</span>
                        @endif
                    </div>
                </div>

                @if($job->decisions->count() > 0)
                <div class="col-md-6 mb-4">
                    <div class="card border-0 h-100 shadow-sm">
                        <div class="card-header bg-primary text-white">
                             <h6 class="mb-0"><i class="fa-solid fa-clock-rotate-left me-2"></i> Approval History</h6>
                        </div>
                        <div class="card-table table-responsive">
                            <table class="table table-sm mb-0" style="font-size: 0.9em;">
                                <thead>
                                    <tr>
                                        <th>Date</th>
                                        <th>Admin</th>
                                        <th>Status</th>
                                        <th>Reason</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    @foreach($decisions as $decision)
                                        <tr>
                                            <td>{{ $decision->created_at->format('M d, Y') }}</td>
                                            <td>{{ $decision->admin->name ?? 'System' }}</td>
                                            <td>
                                                @if($decision->to_status === 'approved')
                                                    <span class="badge bg-success">Approved</span>
                                                @elseif($decision->to_status === 'rejected')
                                                    <span class="badge bg-danger">Rejected</span>
                                                @else
                                                    <span class="badge bg-secondary">{{ $decision->to_status }}</span>
                                                @endif
                                            </td>
                                            <td title="{{ $decision->reason }}">{{ Str::limit($decision->reason, 20) }}</td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer bg-white">
                             {{ $decisions->links() }}
                        </div>
                    </div>
                </div>
                @endif
            </div>

            <!-- Action Buttons -->
            <div class="mt-4 d-flex gap-2">
                @if(auth()->user()->type === 'employer')
                    <a wire:navigate href="{{ route('jobs.edit', $job->id) }}" class="btn btn-warning">
                        <i class="fa-solid fa-pen-to-square me-2"></i> Edit Job
                    </a>
                @endif
                <a wire:navigate href="{{ route('jobs.index') }}" class="btn btn-outline-secondary">
                    <i class="fa-solid fa-list me-2"></i> View All Jobs
                </a>
            </div>
        </div>
    </div>
</div>
