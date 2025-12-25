<div class="container-fluid px-4">
    <div class="d-flex justify-content-between align-items-center mb-4 mt-4">
        <h2 class="text-danger fw-bold">
            <i class="fa-solid fa-user-xmark me-2"></i> Rejected Users Archive
        </h2>
    </div>

    <!-- Search -->
    <div class="card shadow-sm border-0 mb-4">
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <input type="text" wire:model.live.debounce.300ms="search" class="form-control"
                        placeholder="Search by name or email...">
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="card shadow-sm border border-body bg-body text-body rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead class="bg-light">
                        <tr class="border-bottom">
                            <th class="ps-4">Name</th>
                            <th>Email</th>
                            <th>Type</th>
                            <th>Rejection Reason</th>
                            <th>Rejected By (ID)</th>
                            <th>Rejected At</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($rejectedUsers as $user)
                            <tr>
                                <td class="ps-4 fw-bold">{{ $user->name }}</td>
                                <td>{{ $user->email }}</td>
                                <td><span class="badge bg-secondary">{{ ucfirst($user->type) }}</span></td>
                                <td class="text-danger">{{ $user->rejection_reason }}</td>
                                <td>{{ $user->rejected_by }}</td>
                                <td>{{ \Carbon\Carbon::parse($user->rejected_at)->format('Y-m-d H:i') }}</td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="fa-solid fa-box-open fa-2x mb-2"></i>
                                    <p class="mb-0">No rejected users found in archive.</p>
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        <div class="card-footer bg-white border-top-0 py-3">
            {{ $rejectedUsers->links() }}
        </div>
    </div>
</div>