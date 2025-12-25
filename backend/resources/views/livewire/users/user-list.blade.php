{{-- <x-app-layout> --}}
    <div>
        <div class="container-fluid px-3 px-md-4 py-3 bg-body text-body">

            <!-- Header + Search + Buttons -->
            <div
                class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
                <h4 class="fw-semibold mb-0">
                    <i class="fa-solid fa-users me-2 text-primary"></i> Users List
                </h4>
                <div class="d-flex flex-wrap gap-2 align-items-center">
                    <livewire:search.search :search-fields="['name', 'email', 'type', 'status']"
                        emit-event="userSearchUpdated" placeholder="Search..." />

                    <a wire:navigate href="{{ route('users.create') }}" class="btn btn-primary px-4 btn-sm">
                        <i class="fa-solid fa-plus me-2"></i> New
                    </a>
                    <a wire:navigate href="{{ route('users.trash') }}" class="btn btn-outline-secondary px-4 btn-sm">
                        <i class="fa-solid fa-trash me-2"></i> Trash
                    </a>
                </div>
            </div>

            <!-- Users Table -->
            <div class="card shadow-sm border border-body bg-body text-body rounded-3">
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead>
                                <tr class="border-bottom">
                                    <th>#</th>
                                    <th>Name</th>
                                    <th>Email Status</th>
                                    <th>Type</th>
                                    <th>Status</th>
                                    <th>Created At</th>
                                    <th class="text-center">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($users as $index => $user)
                                    <tr>
                                        <td>{{ $users->firstItem() + $index }}</td>
                                        <td>{{ $user->name }}</td>
                                        <td>
                                            <div class="d-flex flex-column">
                                                <span>{{ $user->email }}</span>
                                                <small>
                                                    @if($user->email_verified_at)
                                                        <span class="text-success"><i
                                                                class="fa-solid fa-check-circle me-1"></i>Verified</span>
                                                    @else
                                                        <span class="text-warning"><i
                                                                class="fa-solid fa-clock me-1"></i>Unverified</span>
                                                    @endif
                                                </small>
                                            </div>
                                        </td>
                                        <td>{{ ucfirst($user->type) }}</td>
                                        <td>
                                            @if($user->status === 'approved')
                                                <span class="badge bg-success">Approved</span>
                                            @elseif($user->status === 'pending')
                                                <span class="badge bg-warning text-dark">Pending</span>
                                            @elseif($user->status === 'rejected')
                                                <span class="badge bg-danger">Rejected</span>
                                            @else
                                                <span class="badge bg-secondary">{{ ucfirst($user->status) }}</span>
                                            @endif
                                        </td>
                                        <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                        <td class="text-center">
                                            <div class="btn-group" role="group">
                                                <!-- View Details Button -->
                                                <a wire:navigate href="{{ route('users.show', $user->id) }}"
                                                    class="btn btn-sm btn-info" title="View Details">
                                                    <i class="fa-solid fa-eye"></i>
                                                </a>

                                                <!-- Approve/Reject - ONLY for Employers with Pending status -->
                                                @if($user->type === 'employer' && $user->status === 'pending')
                                                    <button wire:click="approve({{ $user->id }})" class="btn btn-sm btn-success"
                                                        title="Approve">
                                                        <i class="fa-solid fa-check"></i>
                                                    </button>
                                                    <button wire:click="openRejectModal({{ $user->id }}, '{{ $user->name }}')"
                                                        class="btn btn-sm btn-danger" title="Reject">
                                                        <i class="fa-solid fa-times"></i>
                                                    </button>
                                                @endif

                                                <!-- Ban Button - For approved users (not admins) -->
                                                @if($user->status === 'approved' && $user->type !== 'admin')
                                                    <button wire:click="openBanModal({{ $user->id }}, '{{ $user->name }}')"
                                                        class="btn btn-sm btn-dark" title="Ban User">
                                                        <i class="fa-solid fa-ban"></i>
                                                    </button>
                                                @endif

                                                <!-- Unban Button - For banned users -->
                                                @if($user->status === 'banned')
                                                    <button wire:click="unban({{ $user->id }})"
                                                        class="btn btn-sm btn-outline-success" title="Unban User"
                                                        wire:confirm="Are you sure you want to unban this user?">
                                                        <i class="fa-solid fa-unlock"></i>
                                                    </button>
                                                @endif

                                                <!-- Edit Button -->
                                                <a wire:navigate href="{{ route('users.edit', $user->id) }}"
                                                    class="btn btn-sm btn-warning" title="Edit">
                                                    <i class="fa-solid fa-pen-to-square"></i>
                                                </a>

                                                <!-- Delete Button -->
                                                <button class="btn btn-sm btn-secondary"
                                                    wire:click="delete({{ $user->id }})"
                                                    wire:confirm="Are you sure you want to delete this user?"
                                                    title="Delete">
                                                    <i class="fa-solid fa-trash"></i>
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No users found.
                                        </td>
                                    </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <div class="p-3">
                {{ $users->links() }}
            </div>
        </div>

        <!-- Rejection Modal (Livewire Controlled) -->
        @if($showRejectModal)
            <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
                <div class="modal-dialog modal-dialog-centered">
                    <div class="modal-content">
                        <div class="modal-header bg-danger text-white">
                            <h5 class="modal-title">
                                <i class="fa-solid fa-user-xmark me-2"></i> Reject User
                            </h5>
                            <button type="button" class="btn-close btn-close-white" wire:click="closeRejectModal"></button>
                        </div>
                        <form wire:submit="submitReject">
                            <div class="modal-body">
                                <p>You are about to reject: <strong>{{ $rejectUserName }}</strong></p>
                                <div class="mb-3">
                                    <label for="rejectionReason" class="form-label">Rejection Reason <span
                                            class="text-danger">*</span></label>
                                    <textarea wire:model="rejectionReason"
                                        class="form-control @error('rejectionReason') is-invalid @enderror"
                                        id="rejectionReason" rows="4"
                                        placeholder="Please provide a clear reason for rejection (min 10 characters)..."></textarea>
                                    @error('rejectionReason')
                                        <div class="invalid-feedback">{{ $message }}</div>
                                    @enderror
                                    <div class="form-text">This reason will be sent to the user via email.</div>
                                </div>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary"
                                    wire:click="closeRejectModal">Cancel</button>
                                <button type="submit" class="btn btn-danger">
                                    <i class="fa-solid fa-times me-1"></i> Confirm Rejection
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        @endif

        <!-- Ban Modal (Livewire Controlled) -->
        @if($showBanModal)
        <div class="modal fade show d-block" tabindex="-1" style="background-color: rgba(0,0,0,0.5);">
            <div class="modal-dialog modal-dialog-centered">
                <div class="modal-content">
                    <div class="modal-header bg-dark text-white">
                        <h5 class="modal-title">
                            <i class="fa-solid fa-ban me-2"></i> Ban User
                        </h5>
                        <button type="button" class="btn-close btn-close-white" wire:click="closeBanModal"></button>
                    </div>
                    <form wire:submit="submitBan">
                        <div class="modal-body">
                            <p>You are about to ban: <strong>{{ $banUserName }}</strong></p>
                            <div class="alert alert-warning">
                                <i class="fa-solid fa-exclamation-triangle me-2"></i>
                                This user will no longer be able to access their account.
                            </div>
                            <div class="mb-3">
                                <label for="banReason" class="form-label">Ban Reason <span class="text-danger">*</span></label>
                                <textarea wire:model="banReason"
                                    class="form-control @error('banReason') is-invalid @enderror"
                                    id="banReason" rows="4"
                                    placeholder="Please provide a clear reason for ban (min 10 characters)..."></textarea>
                                @error('banReason')
                                    <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                                <div class="form-text">This reason will be sent to the user via email.</div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-secondary" wire:click="closeBanModal">Cancel</button>
                            <button type="submit" class="btn btn-dark">
                                <i class="fa-solid fa-ban me-1"></i> Confirm Ban
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        @endif

        <!-- SweetAlert2 for notifications -->
        <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
        <script>
            document.addEventListener('livewire:init', () => {
                Livewire.on('user-approved', () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'User Approved!',
                        text: 'The user has been approved and notified via email.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });

                Livewire.on('user-rejected', () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'User Rejected!',
                        text: 'The user has been rejected and notified via email.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });

                Livewire.on('user-banned', () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'User Banned!',
                        text: 'The user has been banned and notified via email.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });

                Livewire.on('user-unbanned', () => {
                    Swal.fire({
                        icon: 'success',
                        title: 'User Unbanned!',
                        text: 'The user can now access their account again.',
                        timer: 2000,
                        showConfirmButton: false
                    });
                });

                Livewire.on('user-error', (data) => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: data.message || 'An error occurred.',
                    });
                });
            });
        </script>

        {{-- </x-app-layout> --}}
    </div>
</div>