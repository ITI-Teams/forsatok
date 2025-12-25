<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <h2>
            <i class="fa-solid fa-user me-2"></i> User Details: {{ $this->user->name }}
        </h2>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-1"></i> Back to List
        </a>
    </div>

    <div class="row">
        <!-- Personal Info -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100">
                <div class="card-header bg-white fw-bold">
                    Profile Information
                </div>
                <div class="card-body">
                    <div class="text-center mb-4">
                        <img src="{{ $this->user->avatar_url }}" alt="Avatar" class="rounded-circle mb-2"
                            style="width: 100px; height: 100px; object-fit: cover;">
                        <h4>{{ $this->user->name }}</h4>
                        <span
                            class="badge {{ $this->user->status === 'approved' ? 'bg-success' : ($this->user->status === 'rejected' ? 'bg-danger' : 'bg-warning text-dark') }}">
                            {{ ucfirst($this->user->status) }}
                        </span>
                    </div>

                    <ul class="list-group list-group-flush">
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Email</span>
                            <span>{{ $this->user->email }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Type</span>
                            <span class="fw-bold">{{ ucfirst($this->user->type) }}</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Email Verified</span>
                            <span>
                                @if($this->user->email_verified_at)
                                    <span class="text-success"><i class="fa-solid fa-check me-1"></i>
                                        {{ $this->user->email_verified_at->format('Y-m-d') }}</span>
                                @else
                                    <span class="text-warning">Unverified</span>
                                @endif
                            </span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between">
                            <span class="text-muted">Joined Date</span>
                            <span>{{ $this->user->created_at->format('Y-m-d H:i') }}</span>
                        </li>

                        @if($this->user->status === 'approved' && $this->user->approved_at)
                            <li class="list-group-item d-flex justify-content-between bg-light">
                                <span class="text-muted">Approved By</span>
                                <span>
                                    {{ $this->user->approver->name ?? 'System/Unknown' }}
                                    <br>
                                    <small class="text-muted">{{ $this->user->approved_at->format('Y-m-d H:i') }}</small>
                                </span>
                            </li>
                        @endif
                    </ul>
                </div>
            </div>
        </div>

        <!-- Rejection History (Only for Employers usually, but good for all) -->
        <div class="col-md-6 mb-4">
            <div class="card shadow-sm h-100 border-danger">
                <div class="card-header bg-danger text-white fw-bold">
                    <i class="fa-solid fa-user-clock me-2"></i> Rejection History
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Reason</th>
                                    <th>Rejected By</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($this->rejectedHistory as $history)
                                    <tr>
                                        <td>{{ Str::limit($history->rejection_reason, 50) }}</td>
                                        <td>{{ $history->rejected_by }}</td>
                                        <!-- ID, maybe fetch name if easy, keep simple for now -->
                                        <td>{{ \Carbon\Carbon::parse($history->rejected_at)->format('Y-m-d') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="3" class="text-center text-muted py-4">
                                            No previous rejections found.
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