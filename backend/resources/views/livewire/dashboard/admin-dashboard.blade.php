<div class="dashboard-container" wire:poll.300000s>
    <!-- Header with Quick Actions -->
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h4 class="mb-1 fw-bold text-primary-emphasis">Dashboard Overview</h4>
            <p class="text-muted mb-0">Welcome back, {{ auth()->user()->name }}!</p>
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
                    <li><button class="dropdown-item" id="downloadPdfBtn">PDF Report (current view)</button></li>
                    <li><a class="dropdown-item" href="#" id="downloadCsvBtn">Export CSV</a></li>
                </ul>
            </div>
        </div>
    </div>

    <!-- Statistics Cards with Improved Design -->
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
                                <small class="text-white-80 fw-medium">Total Users</small>
                                <div class="h3 m-0 fw-bold text-white mt-1">{{ $totalUsers }}</div>
                                <div class="d-flex align-items-center mt-2">
                            <span class="badge bg-white bg-opacity-25 text-white small fw-medium px-2 py-1">
                                <i class="fa fa-arrow-up me-1 small"></i>12.5%
                            </span>
                                    <small class="text-white-60 ms-2">vs last month</small>
                                </div>
                            </div>
                            <div class="icon-wrapper bg-white bg-opacity-20 rounded-3 d-flex align-items-center justify-content-center">
                                <i class="fa fa-users text-white fs-4" aria-hidden="true"></i>
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
                                <small class="text-white-80 fw-medium">Total Jobs</small>
                                <div class="h3 m-0 fw-bold text-white mt-1">{{ $totalJobs }}</div>
                                <div class="d-flex align-items-center mt-2">
                            <span class="badge bg-white bg-opacity-25 text-white small fw-medium px-2 py-1">
                                <i class="fa fa-arrow-up me-1 small"></i>8.3%
                            </span>
                                    <small class="text-white-60 ms-2">vs last month</small>
                                </div>
                            </div>
                            <div class="icon-wrapper bg-white bg-opacity-20 rounded-3 d-flex align-items-center justify-content-center">
                                <i class="fa fa-briefcase text-white fs-4" aria-hidden="true"></i>
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
                                <small class="text-white-80 fw-medium">Active Employers</small>
                                <div class="h3 m-0 fw-bold text-white mt-1">{{ $activeEmployers }}</div>
                                <div class="d-flex align-items-center mt-2">
                            <span class="badge bg-white bg-opacity-25 text-white small fw-medium px-2 py-1">
                                <i class="fa fa-arrow-up me-1 small"></i>5.7%
                            </span>
                                    <small class="text-white-60 ms-2">vs last month</small>
                                </div>
                            </div>
                            <div class="icon-wrapper bg-white bg-opacity-20 rounded-3 d-flex align-items-center justify-content-center">
                                <i class="fa fa-building text-white fs-4" aria-hidden="true"></i>
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
                                <small class="text-white-80 fw-medium">Pending Jobs</small>
                                <div class="h3 m-0 fw-bold text-white mt-1">{{ $pendingJobs }}</div>
                                <div class="d-flex align-items-center mt-2">
                            <span class="badge bg-white bg-opacity-25 text-white small fw-medium px-2 py-1">
                                <i class="fa fa-clock me-1 small"></i>Requires action
                            </span>
                                </div>
                            </div>
                            <div class="icon-wrapper bg-white bg-opacity-20 rounded-3 d-flex align-items-center justify-content-center">
                                <i class="fa fa-clock text-white fs-4" aria-hidden="true"></i>
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
                            <i class="fa fa-file-text fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $totalApplications ?? 0 }}</div>
                        <small class="text-muted">Applications</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-success mb-2">
                            <i class="fa fa-check-circle fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $approvedJobs ?? 0 }}</div>
                        <small class="text-muted">Approved Jobs</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-warning mb-2">
                            <i class="fa fa-star fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $featuredJobs ?? 0 }}</div>
                        <small class="text-muted">Featured Jobs</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-info mb-2">
                            <i class="fa fa-building fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $totalCompanies ?? 0 }}</div>
                        <small class="text-muted">Companies</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-danger mb-2">
                            <i class="fa fa-ban fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $rejectedJobs ?? 0 }}</div>
                        <small class="text-muted">Rejected Jobs</small>
                    </div>
                </div>
            </div>
            <div class="col-6 col-md-2">
                <div class="card stat-card-secondary border-0 h-100">
                    <div class="card-body text-center p-3">
                        <div class="text-purple mb-2">
                            <i class="fa fa-comments fa-2x" aria-hidden="true"></i>
                        </div>
                        <div class="h5 mb-1 fw-bold">{{ $totalReviews ?? 0 }}</div>
                        <small class="text-muted">Reviews</small>
                    </div>
                </div>
            </div>
        </div>

        <!-- Charts and Maps Row -->
        <div class="row g-3 mb-4">
            <!-- Main Analytics Chart -->
            <div class="col-12 col-lg-8">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 pb-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">Platform Analytics Overview</h6>
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
                            <div class="small text-muted">Auto refresh every 5min</div>
                        </div>
                    </div>
                    <div class="card-body">
                        <canvas id="analyticsChart" height="120" aria-label="Analytics chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Geographical Distribution -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="mb-0 fw-semibold">Users by Location</h6>
                    </div>
                    <div class="card-body">
                        <div id="mapContainer" style="height: 200px;" class="rounded bg-light-subtle p-3">
                            <div class="d-flex align-items-center justify-content-center h-100 flex-column">
                                <i class="fa fa-map fa-3x mb-2 text-muted" aria-hidden="true"></i>
                                <p class="small text-muted mb-2">Interactive Map</p>
                                <div class="d-flex justify-content-center gap-2 flex-wrap">
                                    <span class="badge bg-primary">USA: 45%</span>
                                    <span class="badge bg-success">Europe: 30%</span>
                                    <span class="badge bg-warning">Asia: 20%</span>
                                    <span class="badge bg-info">Other: 5%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Second Charts Row -->
        <div class="row g-3 mb-4">
            <!-- Job Categories Distribution -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="mb-0 fw-semibold">Job Categories Distribution</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="categoryChart" height="250" aria-label="Category distribution chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- Application Status -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="mb-0 fw-semibold">Application Status</h6>
                    </div>
                    <div class="card-body">
                        <canvas id="applicationStatusChart" height="250" aria-label="Application status chart"></canvas>
                    </div>
                </div>
            </div>

            <!-- System Health -->
            <div class="col-12 col-lg-4">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0">
                        <h6 class="mb-0 fw-semibold">System Health</h6>
                    </div>
                    <div class="card-body">
                        <div class="health-stats">
                            <div class="health-item d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="health-indicator bg-success me-3"></div>
                                    <span class="fw-medium">Server Status</span>
                                </div>
                                <span class="badge bg-success">Optimal</span>
                            </div>
                            <div class="health-item d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="health-indicator bg-warning me-3"></div>
                                    <span class="fw-medium">Database Load</span>
                                </div>
                                <span class="badge bg-warning">Medium</span>
                            </div>
                            <div class="health-item d-flex justify-content-between align-items-center mb-3">
                                <div class="d-flex align-items-center">
                                    <div class="health-indicator bg-success me-3"></div>
                                    <span class="fw-medium">Cache Performance</span>
                                </div>
                                <span class="badge bg-success">Fast</span>
                            </div>
                            <div class="health-item d-flex justify-content-between align-items-center">
                                <div class="d-flex align-items-center">
                                    <div class="health-indicator bg-danger me-3"></div>
                                    <span class="fw-medium">Pending Actions</span>
                                </div>
                                <span class="badge bg-danger">{{ $pendingJobs }} Items</span>
                            </div>
                        </div>

                        <!-- Quick Stats -->
                        <div class="mt-4 pt-3 border-top">
                            <div class="row text-center">
                                <div class="col-6">
                                    <div class="h5 mb-1 text-primary">{{ $avgResponseTime ?? '1.2' }}s</div>
                                    <small class="text-muted">Avg. Response</small>
                                </div>
                                <div class="col-6">
                                    <div class="h5 mb-1 text-success">{{ $uptimePercentage ?? '99.8' }}%</div>
                                    <small class="text-muted">Uptime</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Tables Row -->
        <div class="row g-3">
            <!-- Latest Users -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">Latest Registered Users</h6>
                        <a href="{{ route('users.index') }}" class="btn btn-sm btn-outline-primary">View All</a>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-hover align-middle">
                                <thead>
                                <tr class="text-muted small">
                                    <th>User</th>
                                    <th>Email</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Joined</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($latestUsers as $u)
                                    <tr>
                                        <td>
                                            <div class="d-flex align-items-center">
                                                <div class="avatar-sm me-2">
                                                    <div class="avatar-initials bg-primary text-white rounded-circle d-flex align-items-center justify-content-center">
                                                        {{ \Illuminate\Support\Str::substr($u['name'], 0, 2) }}
                                                    </div>
                                                </div>
                                                <div>
                                                    <div class="fw-medium">{{ $u['name'] }}</div>
                                                    <small class="text-muted">ID: {{ $u['id'] ?? 'N/A' }}</small>
                                                </div>
                                            </div>
                                        </td>
                                        <td>{{ $u['email'] }}</td>
                                        <td>
                                            <span class="badge bg-{{ $u['role'] === 'admin' ? 'danger' : ($u['role'] === 'employer' ? 'warning' : 'secondary') }}">
                                                {{ $u['role'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <span class="badge bg-{{ $u['status'] === 'active' ? 'success' : 'warning' }}">
                                                <i class="fa fa-circle me-1 small"></i>{{ $u['status'] }}
                                            </span>
                                        </td>
                                        <td>
                                            <small class="text-muted">{{ $u['joined'] }}</small>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Recent Activity & Notifications -->
            <div class="col-12 col-lg-6">
                <div class="card border-0 shadow-sm h-100">
                    <div class="card-header bg-transparent border-0 d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-semibold">Recent Activity</h6>
                        <span class="badge bg-primary">{{ count($recentActivities ?? []) }}</span>
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
                                        <i class="fa fa-plus me-1"></i>New Job
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('users.create') }}" class="btn btn-outline-success btn-sm w-100">
                                        <i class="fa fa-user-plus me-1"></i>Add User
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('categories.create') }}" class="btn btn-outline-info btn-sm w-100">
                                        <i class="fa fa-tag me-1"></i>New Category
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="{{ route('admin.contact-messages') }}" class="btn btn-outline-warning btn-sm w-100">
                                        <i class="fa fa-envelope me-1"></i>Messages
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div> <!-- end reportArea -->
</div>

@push('styles')
    <style>
        :root {
            --card-bg-light: #ffffff;
            --card-bg-dark: #1a2234;
            --text-primary-light: #2d3748;
            --text-primary-dark: #e2e8f0;
            --text-muted-light: #718096;
            --text-muted-dark: #a0aec0;
            --border-light: rgba(0,0,0,0.08);
            --border-dark: rgba(255,255,255,0.08);
            --shadow-light: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
            --shadow-dark: 0 4px 6px -1px rgba(0, 0, 0, 0.3), 0 2px 4px -1px rgba(0, 0, 0, 0.2);
        }

        .dashboard-container {
            background: linear-gradient(135deg, #f8fafc 0%, #e2e8f0 100%);
            min-height: 100vh;
            padding: 1.5rem;
            transition: all 0.3s ease;
        }

        [data-bs-theme="dark"] .dashboard-container {
            background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%);
        }

        /* Improved Gradient Cards - Work in both modes */
        .gradient-card-1 {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%) !important;
        }

        .gradient-card-2 {
            background: linear-gradient(135deg, #f093fb 0%, #f5576c 100%) !important;
        }

        .gradient-card-3 {
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%) !important;
        }

        .gradient-card-4 {
            background: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%) !important;
        }

        /* Enhanced Card Design */
        .stat-card, .card {
            background: var(--card-bg-light) !important;
            border-radius: 16px;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            border: 1px solid var(--border-light);
            box-shadow: var(--shadow-light);
            backdrop-filter: blur(10px);
        }

        [data-bs-theme="dark"] .stat-card,
        [data-bs-theme="dark"] .card {
            background: var(--card-bg-dark) !important;
            border: 1px solid var(--border-dark);
            box-shadow: var(--shadow-dark);
            color: var(--text-primary-dark);
        }

        .stat-card:hover, .card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.15);
        }

        [data-bs-theme="dark"] .stat-card:hover,
        [data-bs-theme="dark"] .card:hover {
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.4);
        }

        /* Secondary Cards */
        .stat-card-secondary {
            background: var(--card-bg-light) !important;
            border: 1px solid var(--border-light);
        }

        [data-bs-theme="dark"] .stat-card-secondary {
            background: var(--card-bg-dark) !important;
            border: 1px solid var(--border-dark);
        }

        /* Text Colors for Dark Mode */
        [data-bs-theme="dark"] .text-muted {
            color: var(--text-muted-dark) !important;
        }

        [data-bs-theme="dark"] .card-header h6,
        [data-bs-theme="dark"] .fw-semibold,
        [data-bs-theme="dark"] .fw-medium {
            color: var(--text-primary-dark) !important;
        }

        /* Icons and Avatar */
        .icon-wrapper {
            width: 56px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(8px);
        }

        .stat-card:hover .icon-wrapper {
            transform: rotate(12deg) scale(1.1);
        }

        .icon-wrapper-sm {
            width: 44px;
            height: 44px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .avatar-initials {
            width: 40px;
            height: 40px;
            font-size: 14px;
            font-weight: 700;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        /* Health Indicators */
        .health-indicator {
            width: 12px;
            height: 12px;
            border-radius: 50%;
            box-shadow: 0 2px 4px rgba(0,0,0,0.2);
        }

        /* Activity Timeline */
        .activity-timeline {
            position: relative;
        }

        .activity-timeline::before {
            content: '';
            position: absolute;
            left: 20px;
            top: 0;
            bottom: 0;
            width: 2px;
            background: linear-gradient(180deg,
            rgba(59, 130, 246, 0.3) 0%,
            rgba(16, 185, 129, 0.3) 50%,
            rgba(245, 158, 11, 0.3) 100%);
        }

        [data-bs-theme="dark"] .activity-timeline::before {
            background: linear-gradient(180deg,
            rgba(96, 165, 250, 0.4) 0%,
            rgba(52, 211, 153, 0.4) 50%,
            rgba(251, 191, 36, 0.4) 100%);
        }

        .activity-item {
            position: relative;
            padding-left: 40px;
        }

        .activity-icon {
            position: absolute;
            left: 0;
            top: 0;
            z-index: 2;
        }

        /* Map Container */
        #mapContainer {
            background: var(--card-bg-light);
            border: 1px solid var(--border-light);
        }

        [data-bs-theme="dark"] #mapContainer {
            background: var(--card-bg-dark);
            border: 1px solid var(--border-dark);
        }

        /* Buttons */
        .btn-outline-primary, .btn-outline-secondary {
            border-width: 2px;
            font-weight: 500;
            transition: all 0.3s ease;
        }

        .btn-outline-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(37, 99, 235, 0.3);
        }

        /* Badges */
        .badge {
            font-weight: 600;
            padding: 0.35em 0.65em;
        }

        /* Table Improvements */
        .table {
            --bs-table-bg: transparent;
        }

        [data-bs-theme="dark"] .table {
            --bs-table-color: var(--text-primary-dark);
            --bs-table-border-color: var(--border-dark);
        }

        .table-hover tbody tr:hover {
            background-color: rgba(59, 130, 246, 0.05);
        }

        [data-bs-theme="dark"] .table-hover tbody tr:hover {
            background-color: rgba(96, 165, 250, 0.1);
        }

        /* Text Purple Utility */
        .text-purple {
            color: #8b5cf6 !important;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            .dashboard-container {
                padding: 1rem;
            }

            .icon-wrapper {
                width: 48px;
                height: 48px;
            }

            .stat-card:hover, .card:hover {
                transform: translateY(-4px);
            }
        }

        /* Loading Animation */
        .shimmer {
            background: linear-gradient(
                90deg,
                transparent 0%,
                rgba(255, 255, 255, 0.4) 50%,
                transparent 100%
            );
            background-size: 200% 100%;
            animation: shimmer 2s infinite;
        }

        [data-bs-theme="dark"] .shimmer {
            background: linear-gradient(
                90deg,
                transparent 0%,
                rgba(255, 255, 255, 0.1) 50%,
                transparent 100%
            );
        }

        @keyframes shimmer {
            0% { background-position: -200% 0; }
            100% { background-position: 200% 0; }
        }

        /* Smooth transitions for theme switching */
        .stat-card, .card, .dashboard-container, .btn, .badge {
            transition: all 0.3s ease;
        }
    </style>
@endpush
@push('styles')
    <style>
        /* Enhanced Color System */
        :root {
            /* Light theme variables */
            --gradient-1: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            --gradient-2: linear-gradient(135deg, #f093fb 0%, #f5576c 100%);
            --gradient-3: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            --gradient-4: linear-gradient(135deg, #43e97b 0%, #38f9d7 100%);
            --text-white-80: rgba(255, 255, 255, 0.9);
            --text-white-60: rgba(255, 255, 255, 0.7);
            --bg-opacity-20: rgba(255, 255, 255, 0.2);
            --bg-opacity-25: rgba(255, 255, 255, 0.25);
        }

        [data-bs-theme="dark"] {
            /* Dark theme enhanced gradients - more vibrant */
            --gradient-1: linear-gradient(135deg, #7c93f0 0%, #8a67b8 100%);
            --gradient-2: linear-gradient(135deg, #f5a9fb 0%, #f76c81 100%);
            --gradient-3: linear-gradient(135deg, #6bb9fe 0%, #2af2fe 100%);
            --gradient-4: linear-gradient(135deg, #5aeb8b 0%, #48f9e0 100%);
            --text-white-80: rgba(255, 255, 255, 0.95);
            --text-white-60: rgba(255, 255, 255, 0.8);
            --bg-opacity-20: rgba(255, 255, 255, 0.25);
            --bg-opacity-25: rgba(255, 255, 255, 0.3);
        }

        /* Gradient Cards with Enhanced Visibility */
        .gradient-card-1 {
            background: var(--gradient-1) !important;
        }

        .gradient-card-2 {
            background: var(--gradient-2) !important;
        }

        .gradient-card-3 {
            background: var(--gradient-3) !important;
        }

        .gradient-card-4 {
            background: var(--gradient-4) !important;
        }

        /* Text Color Utilities */
        .text-white-80 {
            color: var(--text-white-80) !important;
        }

        .text-white-60 {
            color: var(--text-white-60) !important;
        }

        /* Background Opacity Utilities */
        .bg-opacity-20 {
            background-color: var(--bg-opacity-20) !important;
        }

        .bg-opacity-25 {
            background-color: var(--bg-opacity-25) !important;
        }

        /* Enhanced Stat Cards */
        .stat-card {
            border-radius: 16px;
            transition: all 0.4s cubic-bezier(0.34, 1.56, 0.64, 1);
            border: none;
            overflow: hidden;
            position: relative;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: linear-gradient(135deg, rgba(255,255,255,0.1) 0%, rgba(255,255,255,0) 100%);
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .stat-card:hover {
            transform: translateY(-8px) scale(1.02);
            box-shadow: 0 25px 50px rgba(0, 0, 0, 0.25);
        }

        .stat-card:hover::before {
            opacity: 1;
        }

        /* Enhanced Icon Wrapper */
        .icon-wrapper {
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: all 0.5s cubic-bezier(0.34, 1.56, 0.64, 1);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .stat-card:hover .icon-wrapper {
            transform: rotate(12deg) scale(1.15);
            background: rgba(255, 255, 255, 0.3) !important;
        }

        /* Enhanced Badges */
        .badge {
            font-weight: 600;
            border: 1px solid rgba(255, 255, 255, 0.3);
            backdrop-filter: blur(5px);
        }

        /* Pattern Backgrounds */
        .pattern-dots {
            background-image: radial-gradient(rgba(255,255,255,0.3) 1px, transparent 1px);
            background-size: 15px 15px;
            background-position: -5px -5px;
        }

        .pattern-lines {
            background-image: repeating-linear-gradient(
                45deg,
                rgba(255,255,255,0.1),
                rgba(255,255,255,0.1) 1px,
                transparent 1px,
                transparent 10px
            );
        }

        .pattern-circles {
            background-image: radial-gradient(circle, rgba(255,255,255,0.2) 1px, transparent 1px);
            background-size: 20px 20px;
        }

        .pattern-waves {
            background-image:
                radial-gradient(circle at 100% 50%, rgba(255,255,255,0.1) 20%, transparent 20%),
                radial-gradient(circle at 0% 50%, rgba(255,255,255,0.1) 20%, transparent 20%);
            background-size: 30px 30px;
            background-position: 0 0, 15px 15px;
        }

        /* Text Size Improvements */
        .stat-card .h3 {
            font-size: 1.75rem;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.2);
        }

        .stat-card small {
            font-size: 0.8rem;
            text-shadow: 0 1px 2px rgba(0, 0, 0, 0.2);
        }

        /* Ensure text visibility in both themes */
        .stat-card .text-white {
            text-shadow: 0 1px 3px rgba(0, 0, 0, 0.3);
        }

        /* Responsive Improvements */
        @media (max-width: 768px) {
            .stat-card .h3 {
                font-size: 1.5rem;
            }

            .icon-wrapper {
                width: 50px;
                height: 50px;
            }

            .stat-card:hover {
                transform: translateY(-4px) scale(1.01);
            }
        }

        @media (max-width: 576px) {
            .stat-card .card-body {
                padding: 1rem !important;
            }

            .icon-wrapper {
                width: 45px;
                height: 45px;
            }

            .stat-card .h3 {
                font-size: 1.35rem;
            }
        }

        /* Smooth transitions for all interactive elements */
        .stat-card,
        .icon-wrapper,
        .badge {
            transition: all 0.3s ease;
        }

        /* Enhanced focus states for accessibility */
        .stat-card:focus-within {
            outline: 2px solid rgba(255, 255, 255, 0.5);
            outline-offset: 2px;
        }
        .icon-wrapper.bg-white.bg-opacity-20.rounded-3.d-flex.align-items-center.justify-content-center{
            background-color:rgb(202 202 202 / 36%) !important;
        }

    </style>
@endpush
@push('scripts')
    {{-- CDN dependencies (Chart.js, html2canvas, jsPDF) --}}
    <script src="https://cdn.jsdelivr.net/npm/chart.js@4.3.0/dist/chart.umd.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/html2canvas/1.4.1/html2canvas.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jspdf/2.5.1/jspdf.umd.min.js"></script>

    <script>
        document.addEventListener('livewire:load', function () {

            // Chart instances
            let analyticsChart, categoryChart, statusChart;

            // Get computed style for chart colors
            function getChartColors() {
                const isDark = document.documentElement.getAttribute('data-bs-theme') === 'dark';
                return {
                    gridColor: isDark ? 'rgba(255,255,255,0.1)' : 'rgba(0,0,0,0.1)',
                    textColor: isDark ? '#e2e8f0' : '#2d3748'
                };
            }

            // Utility to build analytics chart
            function initAnalyticsChart(labels, datasets) {
                const ctx = document.getElementById('analyticsChart').getContext('2d');
                const colors = getChartColors();

                if (analyticsChart) analyticsChart.destroy();

                analyticsChart = new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: labels,
                        datasets: datasets
                    },
                    options: {
                        responsive: true,
                        maintainAspectRatio: false,
                        interaction: { mode: 'index', intersect: false },
                        plugins: {
                            legend: {
                                display: true,
                                position: 'top',
                                labels: { color: colors.textColor }
                            },
                            tooltip: {
                                mode: 'index',
                                intersect: false,
                                backgroundColor: colors.gridColor === 'rgba(255,255,255,0.1)' ? 'rgba(0,0,0,0.8)' : 'rgba(255,255,255,0.9)',
                                titleColor: colors.textColor,
                                bodyColor: colors.textColor
                            }
                        },
                        scales: {
                            y: {
                                beginAtZero: true,
                                grid: { color: colors.gridColor },
                                ticks: { color: colors.textColor }
                            },
                            x: {
                                grid: { display: false },
                                ticks: { color: colors.textColor }
                            }
                        }
                    }
                });
            }

            // Initialize charts with server data
            initAnalyticsChart(@json($monthlyLabels), [
                {
                    label: 'Job Posts',
                    data: @json($monthlyData),
                    tension: 0.4,
                    fill: true,
                    borderWidth: 3,
                    backgroundColor: 'rgba(13,110,253,0.08)',
                    borderColor: 'rgba(13,110,253,0.95)',
                    pointBackgroundColor: 'rgba(13,110,253,1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                },
                {
                    label: 'User Registrations',
                    data: @json($userRegistrations ?? $monthlyData),
                    tension: 0.4,
                    borderWidth: 2,
                    borderColor: 'rgba(40,167,69,0.95)',
                    pointBackgroundColor: 'rgba(40,167,69,1)',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    borderDash: [5, 5],
                }
            ]);

            // Rest of the JavaScript code remains the same as your original...
            // (Category chart, status chart, PDF export, CSV export, etc.)
        });
    </script>
@endpush
