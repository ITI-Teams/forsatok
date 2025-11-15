<div class="dashboard-container" wire:poll.300000s>
    <!-- Header with Quick Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold dashboard-title">Employer Dashboard</h4>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}! Manage your jobs and applications.</p>
        </div>

    </div>

    <!-- Statistics Cards -->
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
                            <i class="fa fa-eye fa-2x"></i>
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
                            <i class="fa fa-star fa-2x"></i>
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
                            <i class="fa fa-clock fa-2x"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $applicationStatusData['pending'] ?? 0 }}</div>
                        <small class="text-muted">Pending</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-info mb-2">
                            <i class="fa fa-calendar fa-2x"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $applicationStatusData['interview'] ?? 0 }}</div>
                        <small class="text-muted">Interview</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-danger mb-2">
                            <i class="fa fa-ban fa-2x"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $applicationStatusData['rejected'] ?? 0 }}</div>
                        <small class="text-muted">Rejected</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-purple mb-2">
                            <i class="fa fa-check-circle fa-2x"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $applicationStatusData['accepted'] ?? 0 }}</div>
                        <small class="text-muted">Accepted</small>
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
                        <canvas id="applicationStatusChart" height="250"></canvas>
                    </div>
                </div>
            </div>
        </div>

        <!-- Recent Applications Table - محسنة -->
        <div class="row g-3 mb-4">
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
                                @forelse($recentApplications as $application)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-initials bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                                        {{ substr($application['applicant'], 0, 2) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $application['applicant'] }}</div>
                                                    <small class="text-muted">{{ $application['email'] }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ \Illuminate\Support\Str::limit($application['job'], 30) }}</td>
                                        <td>
                                            <small class="text-muted">{{ $application['applied_date'] }}</small>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $this->getApplicationStatusColor($application['status']) }}">
                                                {{ ucfirst($application['status']) }}
                                            </span>
                                        </td>
                                        <td>
                                            <div class="d-flex gap-1">
                                                <a href="{{ route('job.app.show', $application['id']) }}" class="btn btn-sm btn-outline-primary">
                                                    <i class="fa fa-eye"></i>
                                                </a>
                                                <a href="{{ route('job.app.edit', $application['id']) }}" class="btn btn-sm btn-outline-success">
                                                    <i class="fa fa-edit"></i>
                                                </a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="5" class="text-center text-muted py-4">
                                            <i class="fa fa-file-text fa-2x mb-2"></i>
                                            <p>No applications yet</p>
                                        </td>
                                    </tr>
                                @endforelse
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
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.1/dist/chart.umd.min.js"></script>

<script>
document.addEventListener('livewire:navigated', function () {
    let applicationsChart, statusChart;

    function initializeCharts() {
        // Applications Chart
        const applicationsCtx = document.getElementById('applicationsChart');
        if (applicationsCtx) {
            applicationsChart = new Chart(applicationsCtx, {
                type: 'line',
                data: {
                    labels: @json($monthlyLabels),
                    datasets: [{
                        label: 'Applications',
                        data: @json($monthlyData),
                        backgroundColor: 'rgba(59, 130, 246, 0.1)',
                        borderColor: 'rgba(59, 130, 246, 1)',
                        borderWidth: 2,
                        tension: 0.4,
                        fill: true
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
                            ticks: {
                                stepSize: 1
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
            statusChart = new Chart(statusCtx, {
                type: 'doughnut',
                data: {
                    labels: ['Pending', 'Accepted', 'Rejected'],
                    datasets: [{
                        data: [
                            statusData.pending || 0,
                            statusData.accepted || 0,
                            statusData.rejected || 0
                        ],
                        backgroundColor: [
                            '#f59e0b', // warning
                            '#10b981', // success
                            '#ef4444'  // danger
                        ],
                        borderWidth: 2,
                        borderColor: '#fff'
                    }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'bottom',
                            labels: {
                                padding: 20,
                                usePointStyle: true
                            }
                        }
                    }
                }
            });
        }
    }

    // Initialize charts on page load
    initializeCharts();

    // Livewire event listener for chart updates
    Livewire.on('updateCharts', (data) => {
        if (applicationsChart) {
            applicationsChart.data.labels = data.labels;
            applicationsChart.data.datasets[0].data = data.data;
            applicationsChart.update();
        }

        if (statusChart) {
            statusChart.data.datasets[0].data = [
                data.status.pending || 0,
                data.status.reviewed || 0,
                data.status.interview || 0,
                data.status.accepted || 0,
                data.status.rejected || 0
            ];
            statusChart.update();
        }
    });

    // Reinitialize charts when Livewire updates the DOM
    document.addEventListener('livewire:load', initializeCharts);
    document.addEventListener('livewire:update', initializeCharts);
});
</script>
@endpush
