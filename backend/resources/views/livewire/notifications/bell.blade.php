<div class="dropdown livewire-notifications">
    <button class="btn btn-light position-relative" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-bell"></i>
        @if($unreadCount > 0)
            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $unreadCount }}</span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg bg-dark text-white" style="width: 360px;">
        <div class="px-3 py-2 d-flex justify-content-between align-items-center border-bottom bg-body dark:bg-gray-800">
            <strong>Notifications</strong>
            <div>
                <button wire:click.prevent="markAllRead" class="btn btn-sm btn-outline-secondary">Mark all read</button>
            </div>
        </div>

        <div style="max-height: 420px; overflow:auto;">
            @forelse($notifications as $n)
                <div class="dropdown-item d-flex align-items-start gap-2 {{ $n['read_at'] ? 'text-muted' : 'text-white' }}">
                    <div class="flex-fill">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="small fw-bold">{{ $n['data']['title'] ?? ($n['data']['message'] ?? $n['type']) }}</div>
                                <div class="small text-truncate" style="max-width: 260px;">
                                    {{ $n['data']['message'] ?? '' }}
                                </div>
                            </div>
                            <div class="text-end small text-muted">{{ $n['created_at'] }}</div>
                        </div>

                        <div class="mt-2 d-flex gap-2">
                            @if(!$n['read_at'])
                                <button wire:click.prevent="markAsRead('{{ $n['id'] }}')" class="btn btn-sm btn-primary">Mark read</button>
                            @endif
                            <a href="{{ $n['data']['url'] ?? '#' }}" class="btn btn-sm btn-outline-secondary">View</a>
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider my-1"></div>
            @empty
                <div class="px-3 py-4 text-center text-muted">No notifications</div>
            @endforelse
        </div>

        <div class="px-3 py-2 border-top text-center">
            <a href="{{ route('notifications.index') }}" class="text-decoration-none text-light">View all</a>
        </div>
    </div>
</div>
