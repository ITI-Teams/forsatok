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
        <div class="col-12 mb-4">
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

        

        <!-- Status History (Approve, Reject, Ban, Unban) -->
        <div class="col-md-12 mb-4">
            <div class="card shadow-sm border-primary">
                <div class="card-header bg-primary text-white fw-bold">
                    <i class="fa-solid fa-clock-rotate-left me-2"></i> Status History (Approve/Reject/Ban/Unban)
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>Action</th>
                                    <th>Reason</th>
                                    <th>Admin</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($this->statusHistory as $record)
                                    <tr>
                                        <td>
                                            @php
                                                $badgeClass = match($record->action) {
                                                    'approved' => 'bg-success',
                                                    'rejected' => 'bg-danger',
                                                    'banned' => 'bg-dark',
                                                    'unbanned' => 'bg-info',
                                                    default => 'bg-secondary'
                                                };
                                            @endphp
                                            <span class="badge {{ $badgeClass }}">
                                                {{ ucfirst($record->action) }}
                                            </span>
                                        </td>
                                        <td>{{ $record->reason ?? '-' }}</td>
                                        <td>
                                            {{ $record->admin_name }}
                                            <br>
                                            <small class="text-muted">{{ $record->admin_email }}</small>
                                        </td>
                                        <td>{{ \Carbon\Carbon::parse($record->created_at)->format('Y-m-d H:i') }}</td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="4" class="text-center text-muted py-4">
                                            No status history found.
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