<div class="container" data-bs-theme="auto">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold text-body">Company Reviews</h1>
        <a href="{{ route('company-reviews.trash') }}" wire:navigate
           class="btn btn-outline-secondary btn-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-trash"></i> View Trash
        </a>
    </div>

    <!-- Search Input -->
    <div class="mb-3">
        <input type="text" wire:model.live="search"
               class="form-control w-50"
               placeholder="🔍 Search review, candidate, company...">
    </div>

    <!-- Flash Messages -->
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

    <!-- Table Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">
        <div class="card-header bg-body-tertiary py-3">
            <h6 class="mb-0 text-secondary fw-semibold">All Company Reviews</h6>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th class="px-4 py-3">Review</th>
                        <th class="px-4 py-3">Rating</th>
                        <th class="px-4 py-3">Candidate</th>
                        <th class="px-4 py-3">Company</th>
                        <th class="px-4 py-3">Created At</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($reviews as $review)
                        <tr>
                            <td class="px-4 py-3">{{ $review->review ?? '-' }}</td>
                            <td class="px-4 py-3">{{ $review->rating }}/5</td>
                            <td class="px-4 py-3">{{ $review->candidate->name }}</td>
                            <td class="px-4 py-3">{{ $review->company->name }}</td>
                            <td class="px-4 py-3">{{ $review->created_at->format('Y-m-d H:i') }}</td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="confirmDelete({{ $review->id }})"
                                        class="btn btn-sm btn-danger rounded-3 px-3 ms-2">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-secondary">
                                No reviews found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        <div class="mt-3">
                {{ $reviews->links() }}
            </div>
    </div>
</div>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This review will be moved to trash.",
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
