<div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Users</small>
                        <div class="h5 m-0 fw-bold">{{ $totalUsers }}</div>
                    </div>
                    <div class="text-primary fs-3"><i class="fa fa-users"></i></div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Total Jobs</small>
                        <div class="h5 m-0 fw-bold">{{ $totalJobs }}</div>
                    </div>
                    <div class="text-success fs-3"><i class="fa fa-briefcase"></i></div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Active Employers</small>
                        <div class="h5 m-0 fw-bold">{{ $activeEmployers }}</div>
                    </div>
                    <div class="text-warning fs-3"><i class="fa fa-building"></i></div>
                </div>
            </div>
        </div>

        <div class="col-6 col-md-3">
            <div class="card stat-card p-3">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <small class="text-muted">Pending Jobs</small>
                        <div class="h5 m-0 fw-bold">{{ $pendingJobs }}</div>
                    </div>
                    <div class="text-danger fs-3"><i class="fa fa-clock"></i></div>
                </div>
            </div>
        </div>
    </div>

    <!-- Chart -->
    <div class="card mb-4 p-3">
        <h6 class="mb-3">Job Postings (last 6 months)</h6>
        <canvas id="jobsChart" height="80"></canvas>
    </div>

    <!-- Latest users table -->
    <div class="card p-3">
        <h6 class="mb-3">Latest Registered Users</h6>
        <div class="table-responsive">
            <table class="table table-borderless align-middle">
                <thead>
                <tr class="text-muted small">
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Joined</th>
                </tr>
                </thead>
                <tbody>
                @foreach($latestUsers as $u)
                    <tr>
                        <td>{{ $u['name'] }}</td>
                        <td>{{ $u['email'] }}</td>
                        <td>{{ $u['role'] }}</td>
                        <td>{{ $u['joined'] }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:load', function () {
                const ctx = document.getElementById('jobsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'line',
                    data: {
                        labels: @json($monthlyLabels),
                        datasets: [{
                            label: 'Job Posts',
                            data: @json($monthlyData),
                            tension: 0.3,
                            fill: true,
                            borderWidth: 2,
                            backgroundColor: 'rgba(13,110,253,0.08)',
                            borderColor: 'rgba(13,110,253,0.9)',
                        }]
                    },
                    options: { responsive: true, plugins: { legend: { display: false } } }
                });
            });
        </script>
    @endpush
</div>
