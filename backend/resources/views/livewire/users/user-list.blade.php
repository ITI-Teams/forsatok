{{-- <x-app-layout> --}}
    <div>
        @if (session()->has('message'))
            <div class="alert alert-success d-flex align-items-center fade show mb-4" role="alert">
                <i class="fa-solid fa-circle-check me-2"></i>
                {{ session('message') }}
            </div>
        @endif

        <div class="d-flex justify-content-between align-items-center mb-4">
            <h4 class="fw-semibold mb-0">
                <i class="fa-solid fa-users me-2 text-primary"></i> Users List
            </h4>
            <a href="{{ route('users.create') }}" class="btn btn-primary px-4">
                <i class="fa-solid fa-plus me-2"></i> New User
            </a>
        </div>

        <div class="card shadow-sm border-0">
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table align-middle mb-0 table-hover">
                        <thead class="table-light">
                            <tr>
                                <th>ID</th>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Type</th>
                                <th>Created At</th>
                                <th class="text-center">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($users as $index => $user)
                                <tr>
                                    <td>{{ $index + 1 }}</td>
                                    <td>{{ $user->name }}</td>
                                    <td>{{ $user->email }}</td>
                                    <td>{{ ucfirst($user->type) }}</td>
                                    <td>{{ $user->created_at->format('Y-m-d') }}</td>
                                    <td class="text-center">
                                        <button class="btn btn-sm btn-info me-2">
                                            <i class="fa-solid fa-eye"></i>
                                        </button>
                                        <button class="btn btn-sm btn-warning me-2">
                                            <i class="fa-solid fa-pen-to-square"></i>
                                        </button>
                                        <button class="btn btn-sm btn-danger" wire:click="delete({{ $user->id }})">
                                            <i class="fa-solid fa-trash"></i>
                                        </button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-4">
                                        No users found.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
{{-- </x-app-layout> --}}
