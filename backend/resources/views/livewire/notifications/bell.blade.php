<div class="dropdown livewire-notifications">
    <button class="btn btn-light dark-btn position-relative" id="notificationsDropdown" data-bs-toggle="dropdown" aria-expanded="false">
        <i class="fa fa-bell"></i>
        @if($unreadCount > 0)
            <span class="badge bg-danger position-absolute top-0 start-100 translate-middle">{{ $unreadCount }}</span>
        @endif
    </button>

    <div class="dropdown-menu dropdown-menu-end p-0 shadow-lg notif-box">
        <div class="px-3 py-2 d-flex justify-content-between align-items-center border-bottom notif-header">
            <strong>Notifications</strong>
            <div>
                <button wire:click.prevent="markAllRead" class="btn btn-sm btn-outline-secondary notif-btn">Mark all read</button>
            </div>
        </div>

        <div class="notif-body">
            @forelse($notifications as $n)
                <div class="dropdown-item d-flex align-items-start gap-2 notif-item {{ $n['read_at'] ? 'notif-read' : 'notif-unread' }}">
                    <div class="flex-fill">
                        <div class="d-flex justify-content-between">
                            <div>
                                <div class="small fw-bold notif-title">
                                    {{ $n['data']['title'] ?? ($n['data']['message'] ?? $n['type']) }}
                                </div>
                                <div class="small text-truncate notif-message" style="max-width: 260px;">
                                    {{ $n['data']['message'] ?? '' }}
                                </div>
                            </div>
                            <div class="text-end small notif-date">
                                {{ $n['created_at'] }}
                            </div>
                        </div>

                        <div class="mt-2 d-flex gap-2">
                            @if(!$n['read_at'])
                                <button wire:click.prevent="markAsRead('{{ $n['id'] }}')" class="btn btn-sm btn-primary notif-btn">Mark read</button>
                            @endif
                            <a href="{{ $n['data']['url'] ?? '#' }}" class="btn btn-sm btn-outline-secondary notif-btn">View</a>
                        </div>
                    </div>
                </div>
                <div class="dropdown-divider my-1 notif-divider"></div>
            @empty
                <div class="px-3 py-4 text-center notif-empty">No notifications</div>
            @endforelse
        </div>

        <div class="px-3 py-2 border-top notif-footer text-center">
            <a href="{{ route('notifications.index') }}" class="text-decoration-none notif-viewall">View all</a>
        </div>
    </div>
</div>
@push('styles')
    /* Base Box */
    .notif-box {
    width: 360px;
    background: var(--notif-bg);
    color: var(--notif-text);
    border-radius: 6px;
    }

    /* Header */
    .notif-header {
    background-color: var(--notif-header-bg);
    color: var(--notif-header-text);
    }

    /* Items */
    .notif-item {
    transition: background 0.2s;
    padding: 10px;
    border-radius: 4px;
    }
    .notif-item:hover {
    background: var(--notif-hover);
    }

    .notif-unread {
    background: var(--notif-unread-bg);
    color: var(--notif-text);
    }
    .notif-read {
    opacity: 0.7;
    }

    /* Text colors */
    .notif-title {
    color: var(--notif-title);
    }
    .notif-message {
    color: var(--notif-message);
    }
    .notif-date {
    color: var(--notif-date);
    }

    /* Divider */
    .notif-divider {
    border-color: var(--notif-divider);
    }

    /* Footer */
    .notif-footer {
    background: var(--notif-footer-bg);
    border-color: var(--notif-divider);
    }
    .notif-viewall {
    color: var(--notif-link);
    }

    /* Empty state */
    .notif-empty {
    color: var(--notif-message);
    }

    /* Buttons */
    .notif-btn {
    color: var(--notif-btn-text) !important;
    border-color: var(--notif-btn-border) !important;
    background: var(--notif-btn-bg) !important;
    }
    .notif-btn:hover {
    background: var(--notif-btn-hover-bg) !important;
    }

    /* ====== LIGHT MODE VARS ====== */
    :root {
    --notif-bg: #ffffff;
    --notif-header-bg: #f8f9fa;
    --notif-header-text: #111;
    --notif-text: #111;
    --notif-title: #111;
    --notif-message: #555;
    --notif-date: #777;
    --notif-hover: #f2f2f2;
    --notif-unread-bg: #eef4ff;
    --notif-divider: #e3e3e3;
    --notif-footer-bg: #f8f9fa;
    --notif-link: #0d6efd;

    --notif-btn-text: #333;
    --notif-btn-border: #ccc;
    --notif-btn-bg: #fff;
    --notif-btn-hover-bg: #f1f1f1;
    }

    /* ====== DARK MODE ====== */
    body.dark-mode {
    --notif-bg: #1e1e1e;
    --notif-header-bg: #2a2a2a;
    --notif-header-text: #eee;
    --notif-text: #eee;
    --notif-title: #fff;
    --notif-message: #ccc;
    --notif-date: #aaa;
    --notif-hover: #333;
    --notif-unread-bg: #2c3e50;
    --notif-divider: #444;
    --notif-footer-bg: #2a2a2a;
    --notif-link: #4aa3ff;

    --notif-btn-text: #ddd;
    --notif-btn-border: #555;
    --notif-btn-bg: #333;
    --notif-btn-hover-bg: #3d3d3d;
    }

    /* Dark Mode Fix for dropdown */
    body.dark-mode .dropdown-menu {
    background: #1e1e1e !important;
    color: #eee !important;
    }

@endpush
