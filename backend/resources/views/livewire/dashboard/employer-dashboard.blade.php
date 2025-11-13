<div>
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3"><div class="card stat-card p-3"><small class="text-muted">My Posted Jobs</small><div class="h5 fw-bold">{{ $myJobsCount }}</div></div></div>
        <div class="col-6 col-md-3"><div class="card stat-card p-3"><small class="text-muted">Active Applications</small><div class="h5 fw-bold">{{ $activeApplications }}</div></div></div>
        <div class="col-6 col-md-3"><div class="card stat-card p-3"><small class="text-muted">Hired Candidates</small><div class="h5 fw-bold">{{ $hiredCandidates }}</div></div></div>
        <div class="col-6 col-md-3"><div class="card stat-card p-3"><small class="text-muted">Pending Reviews</small><div class="h5 fw-bold">{{ $pendingReviews }}</div></div></div>
    </div>

    <div class="card mb-4 p-3">
        <h6 class="mb-3">Applications (last 6 months)</h6>
        <canvas id="appsChart" height="80"></canvas>
    </div>

    <div class="card p-3">
        <h6 class="mb-3">Recent Applications</h6>
        <div class="table-responsive">
            <table class="table table-borderless align-middle">
                <thead><tr class="text-muted small"><th>Applicant</th><th>Job</th><th>Status</th></tr></thead>
                <tbody>
                @foreach($recentApplications as $a)
                    <tr>
                        <td>{{ $a['applicant'] }}</td>
                        <td>{{ $a['job'] }}</td>
                        <td>{{ ucfirst($a['status']) }}</td>
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>

    @push('scripts')
        <script>
            document.addEventListener('livewire:load', function () {
                const ctx = document.getElementById('appsChart').getContext('2d');
                new Chart(ctx, {
                    type: 'bar',
                    data: {
                        labels: @json($monthlyLabels),
                        datasets: [{ label: 'Applications', data: @json($monthlyData), borderRadius:6 }]
                    },
                    options: { responsive: true, plugins: { legend: { display: false } } }
                });
            });
        </script>
    @endpush
</div>
