<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Notifications</h5>
    </div>

    <div class="card-body p-0">
        <table class="table table-hover mb-0">
            <thead class="table-light">
            <tr>
                <th>Title</th>
                <th>Message</th>
                <th>Status</th>
                <th>Date</th>
                <th></th>
            </tr>
            </thead>

            <tbody>
            @forelse($notifications as $notification)
                <tr class="{{ $notification->read_at ? '' : 'table-warning' }}">
                    <td><strong>{{ $notification->data['title'] ?? 'Notification' }}</strong></td>

                    <td>{{ $notification->data['message'] ?? '-' }}</td>

                    <td>
                        @if($notification->read_at)
                            <span class="badge bg-success">Read</span>
                        @else
                            <span class="badge bg-danger">Unread</span>
                        @endif
                    </td>

                    <td>{{ $notification->created_at->format('Y-m-d H:i') }}</td>

                    <td>
                        @if(!$notification->read_at)
                            <button class="btn btn-sm btn-primary"
                                    wire:click="markAsRead('{{ $notification->id }}')">
                                Mark as read
                            </button>
                        @endif
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="text-center text-muted py-3">No notifications found</td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="card-footer">
        {{ $notifications->links() }}
    </div>
</div>
