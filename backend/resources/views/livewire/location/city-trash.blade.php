<div class="container-fluid px-3 px-md-4 py-3 bg-body text-body">
    @if (session()->has('message'))
        <div id="success-toast"
             class="position-fixed top-0 end-0 p-3"
             style="z-index: 1080;">
            <div class="toast show align-items-center text-white bg-success border-0 shadow-lg"
                 role="alert"
                 aria-live="assertive"
                 aria-atomic="true"
                 data-bs-delay="3000"
                 style="min-width: 280px;">
                <div class="d-flex">
                    <div class="toast-body fw-semibold">
                        <i class="fa-solid fa-circle-check me-2"></i>{{ session('message') }}
                    </div>
                    <button type="button" class="btn-close btn-close-white me-2 m-auto"
                            data-bs-dismiss="toast"
                            aria-label="Close"></button>
                </div>
            </div>
        </div>

        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const toastEl = document.querySelector('#success-toast .toast');
                if (toastEl) {
                    const toast = new bootstrap.Toast(toastEl);
                    toast.show();
                    setTimeout(() => {
                        toast.hide();
                    }, 3000);
                }
            });
        </script>
    @endif
    <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-trash-can me-2 text-danger"></i> Deleted Cities
        </h4>
        <a wire:navigate href="{{ route('cities.index') }}" class="btn btn-outline-secondary">
            <i class="fa-solid fa-arrow-left me-2"></i> Back to Cities
        </a>
    </div>

    <div class="card shadow-sm border border-body bg-body text-body rounded-3">
        <div class="card-body p-0">
            @if($trashedCities->isEmpty())
                <div class="text-center py-5">
                    <i class="fa-solid fa-trash-can text-secondary mb-3" style="font-size: 3rem;"></i>
                    <p class="text-secondary mb-0">No deleted cities found</p>
                </div>
            @else
                <div class="table-responsive">
                    <table class="table table-hover align-middle mb-0">
                        <thead class="border-bottom">
                            <tr>
                                <th scope="col" class="text-nowrap py-3">Name</th>
                                <th scope="col" class="text-nowrap">Country</th>
                                <th scope="col" class="text-nowrap">Deleted At</th>
                                <th scope="col" class="text-nowrap text-end">Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach($trashedCities as $city)
                                <tr>
                                    <td class="text-nowrap">{{ $city->name }}</td>
                                    <td class="text-nowrap">{{ $city->country->name ?? '-' }}</td>
                                    <td class="text-nowrap">{{ $city->deleted_at->diffForHumans() }}</td>
                                    <td class="text-nowrap text-end">
                                        <div class="d-flex flex-wrap justify-content-end gap-2">
                                            <button type="button"
                                                    onclick="confirmRestore({{ $city->id }})"
                                                    class="btn btn-sm btn-outline-success d-flex align-items-center"
                                                    >
                                                <i class="fa-solid fa-trash-arrow-up me-1"></i>
                                                Restore
                                            </button>
                                            <button type="button"
                                                    onclick="confirmForceDelete({{ $city->id }})"
                                                    class="btn btn-sm btn-outline-danger d-flex align-items-center"
                                                    >
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

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmRestore(id) {
        Swal.fire({
            title: "Restore City?",
            text: "This city will be restored.",
            icon: "question",
            showCancelButton: true,
            confirmButtonColor: "#28a745",
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
            title: "Are you sure?",
            text: "This city will be permanently deleted and cannot be recovered!",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it permanently!"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('forceDelete', id);
            }
        });
    }
</script>

