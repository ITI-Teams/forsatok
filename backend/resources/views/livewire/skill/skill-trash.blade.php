<div class="container">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold text-body">Trashed Skills</h1>
        <a wire:navigate href="{{ route('skills.index') }}"
           class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Back
        </a>
    </div>

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden bg-body text-body">
        <div class="card-header bg-body-tertiary border-bottom">
            <h6 class="mb-0 text-secondary fw-semibold">Deleted Skills</h6>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-body-tertiary text-secondary">
                    <tr>
                        <th class="px-4 py-3">Name</th>
                        <th class="px-4 py-3">Deleted At</th>
                        <th class="text-center px-4 py-3">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trashedSkills as $skill)
                        <tr>
                            <td class="px-4 py-3">{{ $skill->name }}</td>
                            <td class="px-4 py-3">{{ $skill->deleted_at->diffForHumans() }}</td>
                            <td class="text-center px-4 py-3">
                                <button wire:click="restore({{ $skill->id }})"
                                        class="btn btn-sm btn-success d-inline-flex align-items-center gap-1">
                                    <i class="fa-solid fa-rotate-left"></i> Restore
                                </button>

                                <button onclick="confirmSkillForceDelete({{ $skill->id }})"
                                        class="btn btn-sm btn-danger d-inline-flex align-items-center gap-1 ms-2">
                                    <i class="fa-solid fa-ban"></i> Delete Permanently
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="3" class="text-center text-secondary py-4">No skills in trash.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- SweetAlert Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmSkillForceDelete(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This action will permanently delete this skill!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete permanently"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('forceDelete', id);
            }
        });
    }
</script>
