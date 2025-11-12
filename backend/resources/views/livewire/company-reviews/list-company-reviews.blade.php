<div class="container-fluid px-3 px-md-4 py-3 bg-body text-body">
    @if (session()->has('message'))
        <div class="alert alert-success d-flex align-items-center fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('message') }}
        </div>
    @endif
    @if(session()->has('error'))
        <div class="alert alert-danger d-flex align-items-center fade show mb-4" role="alert">
            <i class="fa-solid fa-triangle-exclamation me-2"></i>
            {{ session('error') }}
        </div>
    @endif

    <!-- Header + Search + Buttons -->
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-star me-2 text-primary"></i> Company Reviews List
        </h4>
        <div class="d-flex flex-wrap gap-2">
            <livewire:search.search
                :search-fields="['review', 'candidate.name', 'company.name']"
                emit-event="companyReviewSearchUpdated"
                placeholder="Search reviews..." />
            <a wire:navigate href="{{ route('company-reviews.trash') }}" class="btn btn-outline-secondary px-4">
                <i class="fa-solid fa-trash me-2"></i> Trashed Reviews
            </a>
        </div>
    </div>

    <!-- Reviews Table -->
    <div class="card shadow-sm border border-body bg-body text-body rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="border-bottom">
                            <th>Review</th>
                            <th>Rating</th>
                            <th>Candidate</th>
                            <th>Company</th>
                            <th>Created At</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($reviews as $review)
                            <tr>
                                <td>{{ $review->review ?? '-' }}</td>
                                <td>{{ $review->rating }}/5</td>
                                <td>{{ $review->candidate->name ?? '-' }}</td>
                                <td>{{ $review->company->name ?? '-' }}</td>
                                <td>{{ $review->created_at->format('Y-m-d') }}</td>
                                <td class="text-center">
                                    <button onclick="confirmDelete({{ $review->id }})"
                                            class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="6" class="text-center text-muted py-4">
                                    No reviews found.
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
        {{ $reviews->links() }}
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
