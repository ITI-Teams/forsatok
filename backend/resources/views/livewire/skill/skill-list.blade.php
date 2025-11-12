<div class="container-fluid px-3 px-md-4 py-3 bg-body text-body">
    @if (session()->has('message'))
        <div class="alert alert-success d-flex align-items-center fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('message') }}
        </div>
    @endif

    <!-- Header + Search + Buttons -->
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-wrench me-2 text-primary"></i> Skills List
        </h4>
        <div class="d-flex flex-wrap gap-2">
            <livewire:search.search :search-fields="['name', 'category.name']" emit-event="skillSearchUpdated"
                placeholder="Search skills..." />

            <a wire:navigate href="{{ route('skills.create') }}" class="btn btn-primary px-4">
                <i class="fa-solid fa-plus me-2"></i> New Skill
            </a>
            <a wire:navigate href="{{ route('skills.trash') }}" class="btn btn-outline-secondary px-4">
                <i class="fa-solid fa-trash me-2"></i> Trashed Skills
            </a>
        </div>
    </div>

    <!-- Skills Table -->
    <div class="card shadow-sm border border-body bg-body text-body rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="border-bottom">
                            <th>Name</th>
                            <th>Category</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($skills as $skill)
                            <tr>
                                <td>{{ $skill->name }}</td>
                                <td>{{ $skill->category ? $skill->category->name : '—' }}</td>
                                <td class="text-center">
                                    <a wire:navigate href="{{ route('skills.edit', $skill->id) }}"
                                        class="btn btn-sm btn-warning me-2">
                                        <i class="fa-solid fa-pen-to-square"></i>
                                    </a>
                                    <button onclick="confirmDelete({{ $skill->id }})" class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-4">No skills found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Pagination -->
    <div class="p-3">
        {{ $skills->links() }}
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
