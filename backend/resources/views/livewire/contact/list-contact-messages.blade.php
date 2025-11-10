<div class="container mt-4">

    <!-- Header -->
    <div class="d-flex align-items-center justify-content-between mb-4">
        <h1 class="h4 fw-bold text-body">Contact Messages</h1>
    </div>

    <!-- Search -->
    <div class="mb-3 w-50">
        <input type="text" wire:model.live="search"
               class="form-control"
               placeholder="🔍 Search by name, email or subject...">
    </div>

    <!-- Success Message -->
    @if (session()->has('success'))
        <div class="alert alert-success border-0 rounded-3 shadow-sm d-flex align-items-center gap-2">
            <i class="fa-solid fa-circle-check"></i> {{ session('success') }}
        </div>
    @endif

    <!-- Table Card -->
    <div class="card shadow-sm border-0 rounded-4 overflow-hidden">

        <div class="card-header bg-body-tertiary py-3">
            <h6 class="mb-0 text-secondary fw-semibold">All Messages</h6>
        </div>

        <div class="card-body p-0">
            <table class="table table-hover mb-0 align-middle">
                <thead class="bg-body-tertiary">
                    <tr>
                        <th class="px-4 py-3">Full Name</th>
                        <th class="px-4 py-3">Email</th>
                        <th class="px-4 py-3">Subject</th>
                        <th class="px-4 py-3">Message</th>
                        <th class="px-4 py-3 text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($messages as $message)
                        <tr>
                            <td class="px-4 py-3">{{ $message->full_name }}</td>
                            <td class="px-4 py-3">{{ $message->email }}</td>
                            <td class="px-4 py-3">{{ $message->subject ?? '-' }}</td>
                            <td class="px-4 py-3 text-truncate" style="max-width: 250px;">
                                {{ $message->message }}
                                @if(strlen($message->message) > 50)
                                    <a href="#" onclick="showMessageModal('{{ addslashes($message->full_name) }}', '{{ addslashes($message->message) }}')">…Read more</a>
                                @endif
                            </td>
                            <td class="px-4 py-3 text-center">
                                <button onclick="confirmDelete({{ $message->id }})"
                                        class="btn btn-sm btn-danger rounded-3 px-3">
                                    <i class="fa-solid fa-trash"></i> Delete
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-secondary">
                                No messages found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        <div class="card-footer d-flex justify-content-end">
            {{ $messages->links() }}
        </div>

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
