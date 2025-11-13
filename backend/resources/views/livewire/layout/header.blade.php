<header class="main-header" id="mainHeader">
    <button id="toggleSidebar" class="btn btn-link text-body fs-4"><i class="fa-solid fa-bars"></i></button>
    <div class="d-flex align-items-center gap-3">

        <!-- Notifications -->
        <div class="dropdown position-relative">
            <button class="btn-icon position-relative" id="notifDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                <i class="fa-solid fa-bell fs-5"></i>
                <span class="notif-badge position-absolute translate-middle badge rounded-pill bg-danger">3</span>
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm animate-dropdown" aria-labelledby="notifDropdown">
                <li><h6 class="dropdown-header">Notifications</h6></li>
                <li><a class="dropdown-item" href="#"><i class="fa-solid fa-circle-info me-2 text-primary"></i> New order received</a></li>
                <li><a class="dropdown-item" href="#"><i class="fa-solid fa-user-plus me-2 text-success"></i> New user registered</a></li>
                <li><hr class="dropdown-divider"></li>
                <li><a class="dropdown-item text-center small text-muted" href="#">View all</a></li>
            </ul>
        </div>

        <!-- Theme toggle -->
        <button id="themeToggle" class="btn btn-link text-body fs-5"><i class="fa-solid fa-moon"></i></button>

        <!-- Profile -->
        <div class="dropdown">
            <button class="btn btn-link d-flex align-items-center text-body" id="profileDropdown" data-bs-toggle="dropdown">
                <img src="https://i.pravatar.cc/40?img=8" class="rounded-circle me-2 border border-2 border-primary-subtle" width="36" height="36">
            </button>
            <ul class="dropdown-menu dropdown-menu-end shadow-sm animate-dropdown" aria-labelledby="profileDropdown">
                @role('employer')
                    <li><a class="dropdown-item" wire:navigate href="{{ route('employer.profile') }}"><i class="fa-solid fa-user me-2"></i> Profile</a></li>
                @endrole
                @role('admin')
                    <li><a class="dropdown-item" wire:navigate href="{{ route('admin.profile') }}"><i class="fa-solid fa-user me-2"></i> Profile</a></li>
                @endrole
                <li><a class="dropdown-item" href="#"><i class="fa-solid fa-gear me-2"></i> Settings</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <a wire:navigate href="{{ route('logout') }}" class="dropdown-item text-danger">
                        <i class="fa-solid fa-right-from-bracket me-2"></i> Logout
                    </a>
                </li>
            </ul>
        </div>
    </div>
</header>
