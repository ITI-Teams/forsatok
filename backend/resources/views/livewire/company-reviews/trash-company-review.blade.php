<div class="container" data-bs-theme="auto">

    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold text-body">Trashed Company Reviews</h1>
        <a href="{{ route('company-reviews.index') }}" wire:navigate
           class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-arrow-left"></i> Back to Reviews
        </a>
    </div>

    @if(session()->has('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger border-0 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-triangle-exclamation"></i> {{ session('error') }}
        </div>
    @endif

    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-body-tertiary py-3">
            <h6 class="mb-0 text-secondary fw-semibold">Trashed Reviews</h6>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th class="px-4 py-3">Review</th>
                        <th class="px-4 py-3">Rating</th>
                        <th class="px-4 py-3">Candidate</th>
                        <th class="px-4 py-3">Company</th>
                        <th class="px-4 py-3">Deleted At</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($trashedReviews as $review)
                        <tr>
                            <td class="px-4 py-3">{{ $review->review ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $review->rating }}/5</td>
                            <td class="px-4 py-3">{{ $review->candidate->name }}</td>
                            <td class="px-4 py-3">{{ $review->company->name }}</td>
                            <td class="px-4 py-3">{{ $review->deleted_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="confirmRestore({{ $review->id }})"
                                        class="btn btn-sm btn-success rounded-3 px-3">
                                    <i class="fa-solid fa-rotate-left"></i> Restore
                                </button>
                                <button onclick="confirmForceDelete({{ $review->id }})"
                                        class="btn btn-sm btn-danger rounded-3 px-3 ms-2">
                                    <i class="fa-solid fa-trash"></i> Delete Permanently
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary">
                                No trashed reviews found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
                {{ $trashedReviews->links() }}
            </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmRestore(id) {
        Swal.fire({
            title: "Restore Review?",
            text: "This review will be restored.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#198754",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, restore it!"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('restore', id);
            }
        });
    }

    function confirmForceDelete(id) {
        Swal.fire({
            title: "Delete Permanently?",
            text: "This review cannot be restored!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete!"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('forceDelete', id);
            }
        });
    }
</script>
