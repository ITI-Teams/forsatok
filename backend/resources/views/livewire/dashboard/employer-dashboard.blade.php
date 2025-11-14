<div class="dashboard-container" wire:poll.300000s>
    <!-- Header with Quick Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold dashboard-title">Employer Dashboard</h4>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}! Manage your jobs and applications.</p>
        </div>

        <div class="d-flex gap-2 align-items-center">
            <button class="btn btn-outline-primary btn-sm" wire:click="$refresh" title="Refresh dashboard">
                <i class="fa fa-refresh me-1"></i>Refresh
            </button>

            <div class="dropdown">
                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" id="exportDropdown"
                        data-bs-toggle="dropdown" aria-expanded="false">
                    <i class="fa fa-download me-1"></i>Export
                </button>
                <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="exportDropdown">
                    <li><button class="dropdown-item" id="downloadPdfBtn">PDF Report</button></li>
                    <li><a class="dropdown-item" href="#" id="downloadCsvBtn">Export CSV</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistics Cards with Employer Focus -->
    <div id="reportArea">
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-3">
                <div class="card stat-card gradient-card-1 border-0 shadow-sm position-relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="position-absolute top-0 end-0 w-100 h-100 opacity-10">
                        <div class="pattern-dots"></div>
                    </div>

                    <div class="card-body p-3 position-relative">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <small class="text-white-80 fw-medium">My Jobs</small>
                                <div class="h3 m-0 fw-bold text-white mt-1">{{ $myJobsCount }}</div>
                                <div class="d-flex align-items-center mt-2">
                                    <span class="badge bg-white bg-opacity-25 text-white small fw-medium px-2 py-1">
                                        <i class="fa fa-briefcase me-1 small"></i>Active
                                    </span>
                                </div>
                            </div>
                            <div class="icon-wrapper bg-opacity-20 rounded-3 d-flex align-items-center justify-content-center">
                                <i class="fa fa-briefcase fs-4" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card gradient-card-2 border-0 shadow-sm position-relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="position-absolute top-0 end-0 w-100 h-100 opacity-10">
                        <div class="pattern-lines"></div>
                    </div>

                    <div class="card-body p-3 position-relative">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <small class="text-white-80 fw-medium">Active Applications</small>
                                <div class="h3 m-0 fw-bold text-white mt-1">{{ $activeApplications }}</div>
                                <div class="d-flex align-items-center mt-2">
                                    <span class="badge bg-white bg-opacity-25 text-white small fw-medium px-2 py-1">
                                        <i class="fa fa-clock me-1 small"></i>Pending review
                                    </span>
                                </div>
                            </div>
                            <div class="icon-wrapper  bg-opacity-20 rounded-3 d-flex align-items-center justify-content-center">
                                <i class="fa fa-file-text  fs-4" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card gradient-card-3 border-0 shadow-sm position-relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="position-absolute top-0 end-0 w-100 h-100 opacity-10">
                        <div class="pattern-circles"></div>
                    </div>

                    <div class="card-body p-3 position-relative">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <small class="text-white-80 fw-medium">Hired Candidates</small>
                                <div class="h3 m-0 fw-bold text-white mt-1">{{ $hiredCandidates }}</div>
                                <div class="d-flex align-items-center mt-2">
                                    <span class="badge bg-white bg-opacity-25 text-white small fw-medium px-2 py-1">
                                        <i class="fa fa-check me-1 small"></i>Successful
                                    </span>
                                </div>
                            </div>
                            <div class="icon-wrapper bg-opacity-20 rounded-3 d-flex align-items-center justify-content-center">
                                <i class="fa fa-users fs-4" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="col-6 col-md-3">
                <div class="card stat-card gradient-card-4 border-0 shadow-sm position-relative overflow-hidden">
                    <!-- Background Pattern -->
                    <div class="position-absolute top-0 end-0 w-100 h-100 opacity-10">
                        <div class="pattern-waves"></div>
                    </div>

                    <div class="card-body p-3 position-relative">
                        <div class="d-flex justify-content-between align-items-start">
                            <div class="flex-grow-1">
                                <small class="text-white-80 fw-medium">Response Rate</small>
                                <div class="h3 m-0 fw-bold text-white mt-1">{{ $responseRate }}%</div>
                                <div class="d-flex align-items-center mt-2">
                                    <span class="badge bg-white bg-opacity-25 text-white small fw-medium px-2 py-1">
                                        <i class="fa fa-chart-line me-1 small"></i>Performance
                                    </span>
                                </div>
                            </div>
                            <div class="icon-wrapper  bg-opacity-20 rounded-3 d-flex align-items-center justify-content-center">
                                <i class="fa fa-chart-pie  fs-4" aria-hidden="true"></i>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Additional Stats Row -->
        <div class="row g-3 mb-4">
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-primary mb-2">
                            <i class="fa fa-eye fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ number_format($totalViews) }}</div>
                        <small class="text-muted">Total Views</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-success mb-2">
                            <i class="fa fa-star fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $pendingReviews }}</div>
                        <small class="text-muted">Pending Reviews</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-warning mb-2">
                            <i class="fa fa-clock fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $activeApplications }}</div>
                        <small class="text-muted">To Review</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-info mb-2">
                            <i class="fa fa-calendar fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $hiredCandidates }}</div>
                        <small class="text-muted">Hired This Month</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-danger mb-2">
                            <i class="fa fa-ban fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">0</div>
                        <small class="text-muted">Rejected</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-purple mb-2">
                            <i class="fa fa-comments fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">12</div>
                        <small class="text-muted">Messages</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Analytics Row -->
        <div class="row g-3 mb-4">
            <!-- Applications Chart -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">Applications Overview</h6>
                        <div class="d-flex align-items-center gap-2">
                            <div class="dropdown">
                                <button class="btn btn-outline-secondary btn-sm dropdown-toggle" type="button" data-bs-toggle="dropdown">
                                    Last 6 Months
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item" href="#" wire:click.prevent="changeTimeRange('7days')">Last 7 Days</a></li>
                                    <li><a class="dropdown-item" href="#" wire:click.prevent="changeTimeRange('30days')">Last 30 Days</a></li>
                                    <li><a class="dropdown-item" href="#" wire:click.prevent="changeTimeRange('6months')">Last 6 Months</a></li>
                                    <li><a class="dropdown-item" href="#" wire:click.prevent="changeTimeRange('1year')">Last Year</a></li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="applicationsChart" height="120" aria-label="Applications chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Application Status Distribution -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="mb-0 fw-semibold">Application Status</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="applicationStatusChart" height="200" aria-label="Application status distribution"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Row: Top Jobs & Recent Activity -->
        <div class="row g-3 mb-4">
            <!-- Top Performing Jobs -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">Top Performing Jobs</h6>
                        <a href="{{ route('jobs.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                <tr class="text-muted small">
                                    <th>Job Title</th>
                                    <th>Applications</th>
                                    <th>Views</th>
                                    <th>Status</th>
                                </tr>
                                </thead>
                                <tbody>
                                @php
                                    $maxApplications = count($topPerformingJobs) > 0 ? max(array_column($topPerformingJobs, 'applications')) : 0;
                                @endphp

                                @forelse($topPerformingJobs as $job)
                                    <tr>
                                        <td>
                                            <div class="fw-medium">{{ \Illuminate\Support\Str::limit($job['title'], 30) }}</div>
                                        </td>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <span class="me-2">{{ $job['applications'] }}</span>
                                                <div class="progress flex-grow-1" style="height: 6px;">
                                                    @if($maxApplications > 0)
                                                        <div class="progress-bar bg-primary" style="width: {{ ($job['applications'] / $maxApplications) * 100 }}%"></div>
                                                    @else
                                                        <div class="progress-bar bg-primary" style="width: 0%"></div>
                                                    @endif
                                                </div>
                                            </div>
                                        </td>
                                        <td>
                                            <span class="badge bg-light text-dark">{{ $job['views'] }}</span>
                                        </td>
                                        <td>
                            <span class="badge bg-{{ $job['status_color'] }}">
                                {{ ucfirst($job['status']) }}
                            </span>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            <i class="fa fa-briefcase fa-2x mb-2"></i>
                                            <p>No jobs posted yet</p>
                                            <a href="{{ route('jobs.create') }}" class="btn btn-sm btn-primary">Post Your First Job</a>
                                        </td>
                                    </tr>
                                @endforelse
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Quick Actions -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">Recent Activity</h6>
                        <span class="badge bg-primary">{{ count($recentActivities) }}</span>
                    </div>
                    <div class="card-body">
                        <div class="activity-timeline">
                            @foreach($recentActivities as $activity)
                                <div class="activity-item d-flex align-items-start mb-3">
                                    <div class="activity-icon me-3">
                                        <div class="icon-wrapper-sm bg-{{ $activity['color'] }} bg-opacity-10 rounded-circle d-flex align-items-center justify-content-center">
                                            <i class="fa fa-{{ $activity['icon'] }} text-{{ $activity['color'] }}"></i>
                                        </div>
                                    </div>
                                    <div class="activity-content flex-grow-1">
                                        <p class="mb-1 small fw-medium">{{ $activity['description'] }}</p>
                                        <small class="text-muted">{{ $activity['time'] }}</small>
                                    </div>
                                </div>
                            @endforeach
                        </div>

                        <!-- Quick Actions -->
                        <div class="mt-4 pt-3 border-top">
                            <h6 class="small text-muted mb-3">Quick Actions</h6>
                            <div class="row g-2">
                                <div class="col-6">
                                    <a href="{{ route('jobs.create') }}" class="btn btn-outline-primary btn-sm w-100">
                                        <i class="fa fa-plus me-1"></i>Post New Job
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('job.app.index') }}" class="btn btn-outline-success btn-sm w-100">
                                        <i class="fa fa-list me-1"></i>View Applications
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('employer.profile.edit') }}" class="btn btn-outline-info btn-sm w-100">
                                        <i class="fa fa-edit me-1"></i>Edit Profile
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('company-reviews.index') }}" class="btn btn-outline-warning btn-sm w-100">
                                        <i class="fa fa-star me-1"></i>Company Reviews
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Applications Table -->
        <div class="row g-3">
            <div class="col-12">
                <div class="card border-0 shadow-sm">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">Recent Applications</h6>
                        <a href="{{ route('job.app.index') }}" class="btn btn-sm btn-outline-primary">View All Applications</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                <tr class="text-muted small">
                                    <th>Applicant</th>
                                    <th>Job Position</th>
                                    <th>Applied Date</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($recentApplications as $application)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-initials bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                                        {{ \Illuminate\Support\Str::substr($application['applicant'], 0, 2) }}
                                                    </div>
                                                </div>
                                                <div class="fw-medium">{{ $application['applicant'] }}</div>
                                            </div>
                                        </td>
                                        <td>{{ $application['job'] }}</td>
                                        <td>
                                            <small class="text-muted">{{ now()->subDays(rand(1, 30))->format('M d, Y') }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $this->getApplicationStatusColor($application['status']) }}">
                                                {{ ucfirst($application['status']) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="#" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="#" class="btn btn-sm btn-outline-success">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('styles')
    <style>
        /* Employer Dashboard Specific Styles */
        .employer-dashboard {
            background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        }

        [data-bs-theme="dark"] .employer-dashboard {
            background: linear-gradient(135deg, #0c4a6e 0%, #075985 100%);
        }

        /* Custom gradient colors for employer dashboard */
        .gradient-card-1 {
            background: linear-gradient(135deg, #3b82f6 0%, #1d4ed8 100%) !important;
        }

        .gradient-card-2 {
            background: linear-gradient(135deg, #10b981 0%, #047857 100%) !important;
        }

        .gradient-card-3 {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
        }

        .gradient-card-4 {
            background: linear-gradient(135deg, #8b5cf6 0%, #7c3aed 100%) !important;
        }

        /* Progress bars in tables */
        .progress {
            background-color: var(--bs-light);
            border-radius: 10px;
        }

        .progress-bar {
            border-radius: 10px;
        }

        /* Application status colors */
        .badge.bg-pending { background-color: #f59e0b !important; }
        .badge.bg-reviewed { background-color: #3b82f6 !important; }
        .badge.bg-interview { background-color: #8b5cf6 !important; }
        .badge.bg-hired { background-color: #10b981 !important; }
        .badge.bg-rejected { background-color: #ef4444 !important; }
    </style>
@endpush
@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
<script>
    document.addEventListener('livewire:load', function () {
        // Applications Chart
        const applicationsCtx = document.getElementById('applicationsChart');
        if (applicationsCtx) {
            new Chart(applicationsCtx.getContext('2d'), {
                type: 'bar',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [{
                        label: 'Applications',
                        data: @json($monthlyData),
                        backgroundColor: 'rgba(59, 130, 246, 0.6)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        borderRadius: 6,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            display: false
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(0,0,0,0.1)'
                            }
                        },
                        x: {
                            grid: {
                                display: false
                            }
                        }
                    }
                }
            });
        }

        // Application Status Chart
        const statusCtx = document.getElementById('applicationStatusChart');
        if (statusCtx) {
            const statusData = @json($applicationStatusData);
            new Chart(statusCtx.getContext('2d'), {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Reviewed', 'Interview', 'Hired', 'Rejected'],
                    datasets: [{
                        data: [
                            statusData.pending || 0,
                            statusData.reviewed || 0,
                            statusData.interview || 0,
                            statusData.hired || 0,
                            statusData.rejected || 0
                        ],
                        backgroundColor: [
                            '#f59e0b',
                            '#3b82f6',
                            '#8b5cf6',
                            '#10b981',
                            '#ef4444'
                        ],
                        borderWidth: 0,
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                usePointStyle: true,
                                padding: 20
                            }
                        }
                    },
                    cutout: '65%'
                }
            });
        }
    });
</script>
@endpush
