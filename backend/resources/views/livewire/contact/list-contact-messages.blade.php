<div class="container-fluid px-3 px-md-4 py-3 bg-body text-body">
    @if (session()->has('success'))
        <div class="alert alert-success d-flex align-items-center fade show mb-4" role="alert">
            <i class="fa-solid fa-circle-check me-2"></i>
            {{ session('success') }}
        </div>
    @endif

    <!-- Header + Search -->
    <div
        class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-4 gap-3">
        <h4 class="fw-semibold mb-0">
            <i class="fa-solid fa-envelope me-2 text-primary"></i> Contact Messages
        </h4>
        <div class="d-flex flex-wrap gap-2">
            <input type="text" wire:model.live="search" class="form-control"
                placeholder="Search by name, email or subject...">
        </div>
    </div>

    <!-- Messages Table -->
    <div class="card shadow-sm border border-body bg-body text-body rounded-3">
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover align-middle mb-0">
                    <thead>
                        <tr class="border-bottom">
                            <th>Full Name</th>
                            <th>Email</th>
                            <th>Subject</th>
                            <th>Message</th>
                            <th class="text-center">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($messages as $message)
                            <tr>
                                <td>{{ $message->full_name }}</td>
                                <td>{{ $message->email }}</td>
                                <td>{{ $message->subject ?? '-' }}</td>
                                <td class="text-truncate" style="max-width: 250px;">
                                    {{ $message->message }}
                                    @if(strlen($message->message) > 50)
                                        <a href="#"
                                            onclick="showMessageModal('{{ addslashes($message->full_name) }}', '{{ addslashes($message->message) }}')">…Read
                                            more</a>
                                    @endif
                                </td>
                                <td class="text-center">
                                    <button onclick="confirmDelete({{ $message->id }})"
                                        class="btn btn-sm btn-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted py-4">
                                    No messages found.
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
        {{ $messages->links() }}
    </div>
</div>

<!-- SweetAlert2 Script -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script>
    function confirmDelete(id) {
        Swal.fire({
            title: "Are you sure?",
            text: "This message will be deleted permanently.",
            icon: "warning",
            showCancelButton: true,
            confirmButtonColor: "#d33",
            cancelButtonColor: "#6c757d",
            confirmButtonText: "Yes, delete it!"
        }).then((result) => {
            if (result.isConfirmed) {
                @this.call('deleteMessage', id);
            }
        });
    }

    function showMessageModal(name, message) {
        Swal.fire({
            title: name,
            html: message.replace(/\n/g, "<br>"),
            icon: 'info',
            confirmButtonText: 'Close',
            width: '600px'
        });
    }
</script>
