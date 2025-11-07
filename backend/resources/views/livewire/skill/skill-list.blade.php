<div class="container" data-bs-theme="auto">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold text-body">Skills</h1>
        <a wire:navigate href="{{ route('skills.trash') }}" class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-trash"></i> View Trash
        </a>
    </div>

    <!-- Search + Add -->
    <div class="d-flex justify-content-between mb-3">
        <input type="text" wire:model.live="search" class="form-control w-50"
               placeholder="🔍 Search for skill...">

        <a wire:navigate href="{{ route('skills.create') }}" class="btn btn-primary d-flex align-items-center gap-2">
            <i class="fa-solid fa-plus"></i> Add New Skill
        </a>
    </div>

    <!-- Success Message -->
    @if (session()->has('message'))
        <div class="alert alert-success border-0 shadow-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('message') }}
        </div>
    @endif

    <!-- Skills Table -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-body text-body">
        <div class="card-header bg-body-tertiary border-bottom py-3">
            <h6 class="mb-0 text-secondary fw-semibold">All Skills</h6>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-body-tertiary text-secondary">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Category</th>
                        <th class="text-center px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($skills as $skill)
                        <tr>
                            <td class="px-4 py-3">{{ $skill->name }}</td>
                            <td class="px-4 py-3">
                                {{ $skill->category ? $skill->category->name : '—' }}
                            </td>
                            <td class="text-center px-4 py-3">
                                <a wire:navigate href="{{ route('skills.edit', $skill->id) }}"
                                   class="btn btn-sm btn-primary px-3">
                                    <i class="fa-solid fa-pen"></i> Edit
                                </a>
                                <button onclick="confirmDelete({{ $skill->id }})"
                                        class="btn btn-sm btn-danger px-3 ms-2">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-secondary py-4">No skills found.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This skill will be moved to trash.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('delete', id);
            }
        });
    }
</script>
