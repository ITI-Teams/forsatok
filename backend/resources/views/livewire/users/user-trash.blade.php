{{-- <x-app-layout> --}}
    <div class="container-fluid px-3 px-md-4 py-3 bg-body text-body">
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-trash-can me-2 text-danger"></i> Deleted Users
        </h4>
        <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Back to Users
        </a>
    </div>

    @if (session('message'))
        <div class="alert alert-success alert-dismissible fade show d-flex align-items-center" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            <div>{{ session('message') }}</div>
            <button type="button" class="btn-close ms-auto" data-bs-dismiss="alert" aria-label="Close"></button>
        </div>
    @endif

    <div class="card shadow-sm border border-body bg-body text-body rounded-3">
        <div class="card-body p-0">
            @if($trashedUsers->isEmpty())
                <div class="text-center py-5">
                    <i class="fa-solid fa-trash-can text-secondary mb-3" style="font-size: 3rem;"></i>
                    <p class="text-secondary mb-0">No deleted users found</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="border-bottom">
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
                                        <div class="d-flex flex-wrap justify-content-end gap-2">
                                            <button type="button" 
                                                    wire:click="restore({{ $user->id }})"
                                                    class="btn btn-sm btn-outline-success d-flex align-items-center"
                                                    onclick="confirm('Are you sure you want to restore this user?') || event.stopImmediatePropagation()">
                                                <i class="fa-solid fa-trash-arrow-up me-1"></i>
                                                Restore
                                            </button>
                                            <button type="button"
                                                    wire:click="forceDelete({{ $user->id }})"
                                                    class="btn btn-sm btn-outline-danger d-flex align-items-center"
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