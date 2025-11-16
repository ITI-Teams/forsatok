<header class="main-header" id="mainHeader">
    <button id="toggleSidebar" class="btn btn-link text-body fs-4"><i class="fa-solid fa-bars"></i></button>
    <div class="d-flex align-items-center gap-3">

        <!-- Notifications -->
        <div class="dropdown position-relative">
            @livewire('notifications.bell')
        </div>

        <!-- Theme toggle -->
        <button id="themeToggle" class="btn btn-link text-body fs-5"><i class="fa-solid fa-moon"></i></button>

        <!-- Profile -->
        <div class="dropdown">
            <button class="btn btn-link d-flex align-items-center text-body" id="profileDropdown" data-bs-toggle="dropdown">
                @if(auth()->user()->avatar)
                    <img src="{{ Storage::disk('public')->url(auth()->user()->avatar) }}"
                         class="rounded-circle me-2 border border-2 border-primary-subtle" width="36" height="36">
                @else
                    <img src="{{ asset('storage/avatars/avatar.svg') }}"
                         class="rounded-circle me-2 border border-2 border-primary-subtle" width="36" height="36">
                @endif
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
