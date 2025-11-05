{{-- <x-app-layout> --}}
    <div>
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-semibold mb-0">
                <i class="fa-solid fa-trash-can me-2 text-danger"></i>Deleted Users
            </h4>
            <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
                <i class="fa-solid fa-arrow-left me-2"></i>Back to Users
            </a>
        </div>

        @if (session('message'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('message') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                @if($trashedUsers->isEmpty())
                    <div class="text-center py-5">
                        <i class="fa-solid fa-trash-can text-muted mb-3" style="font-size: 3rem;"></i>
                        <p class="text-muted mb-0">No deleted users found</p>
                    </div>
                @else
                    <div class="table-responsive">
                        <table class="table table-hover align-middle mb-0">
                            <thead class="bg-light">
                                <tr>
                                    <th scope="col" class="text-nowrap py-3">Name</th>
                                    <th scope="col" class="text-nowrap">Email</th>
                                    <th scope="col" class="text-nowrap">Type</th>
                                    <th scope="col" class="text-nowrap">Deleted At</th>
                                    <th scope="col" class="text-nowrap text-end">Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($trashedUsers as $user)
                                    <tr>
                                        <td class="text-nowrap">
                                            <div class="d-flex align-items-center">
                                                <div class="avatar avatar-sm bg-primary-subtle rounded-circle me-2 d-flex align-items-center justify-content-center">
                                                    <i class="fa-solid fa-user text-primary small"></i>
                                                </div>
                                                {{ $user->name }}
                                            </div>
                                        </td>
                                        <td class="text-nowrap">{{ $user->email }}</td>
                                        <td class="text-nowrap">
                                            <span class="badge bg-{{ $user->type === 'admin' ? 'danger' : ($user->type === 'employer' ? 'primary' : 'success') }}">
                                                {{ ucfirst($user->type) }}
                                            </span>
                                        </td>
                                        <td class="text-nowrap">{{ $user->deleted_at->diffForHumans() }}</td>
                                        <td class="text-nowrap text-end">
                                            <div class="btn-group">
                                                <button type="button" 
                                                        wire:click="restore({{ $user->id }})"
                                                        class="btn btn-sm btn-outline-success"
                                                        onclick="confirm('Are you sure you want to restore this user?') || event.stopImmediatePropagation()">
                                                    <i class="fa-solid fa-trash-arrow-up me-1"></i>
                                                    Restore
                                                </button>
                                                <button type="button"
                                                        wire:click="forceDelete({{ $user->id }})"
                                                        class="btn btn-sm btn-outline-danger"
                                                        onclick="confirm('This action cannot be undone. Delete permanently?') || event.stopImmediatePropagation()">
                                                    <i class="fa-solid fa-trash me-1"></i>
                                                    Delete
                                                </button>
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @endif
            </div>
        </div>
    </div>
{{-- </x-app-layout> --}}