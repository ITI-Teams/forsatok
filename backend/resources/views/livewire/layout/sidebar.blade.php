<nav class="sidebar d-flex flex-column overflow-hidden" id="sidebar">
    <div class="text-center py-3 fs-5 fw-bold text-primary border-bottom">F</div>

    <ul class="nav flex-column mt-2">
        <li><a wire:navigate href="{{ route('dashboard') }}"
                class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i
                    class="fa-solid fa-gauge-high"></i><span>Dashboard</span></a></li>
        <li><a wire:navigate href="{{ route('admin') }}"
                class="nav-link {{ request()->routeIs('admin') ? 'active' : '' }}"><i
                    class="fa-solid fa-user-gear"></i><span>Admins</span></a></li>
        <li><a wire:navigate href="{{ route('employer.profile') }}"
                class="nav-link {{ request()->routeIs('employer.profile') ? 'active' : '' }}"><i
                    class="bi bi-person-badge"></i><span>Employer Profile</span></a></li>
        <li><a wire:navigate href="{{ route('list') }}"
                class="nav-link {{ request()->routeIs('list') ? 'active' : '' }}"><i
                    class="fa-solid fa-user-gear"></i><span>Lists</span></a></li>
        <li><a wire:navigate href="{{ route('form') }}"
                class="nav-link {{ request()->routeIs('form') ? 'active' : '' }}"><i
                    class="fa-solid fa-user-gear"></i><span>Forms</span></a></li>
        <li>
            <a wire:navigate href="{{ route('jobs.index') }}"
                class="nav-link {{ request()->routeIs('jobs.index') ? 'active' : '' }}">
                <i class="fa-solid fa-user-gear"></i><span>Job Lists</span>
            </a>
        </li>
        <li>
            <a wire:navigate href="{{ route('job.app.index') }}"
               class="nav-link {{ request()->routeIs('job.app.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-gear"></i><span>Jobs Applications</span>
            </a>
        </li>

        {{-- Dashboard --}}
        <li>
            <a wire:navigate href="{{ route('dashboard') }}"
               class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}">
                <i class="fa-solid fa-gauge-high"></i>
                <span>Dashboard</span>
            </a>
        </li>
        {{-- Categories --}}
        <li>
            <a wire:navigate href="{{ route('categories.index') }}"
               class="nav-link {{ request()->routeIs('categories.*') ? 'active' : '' }}">
                <i class="fa-solid fa-layer-group"></i>
                <span>Categories</span>

            </a>
        </li>
        {{-- Skills --}}
        <li>
            <a wire:navigate href="{{ route('skills.index') }}"
               class="nav-link {{ request()->routeIs('skills.*') ? 'active' : '' }}">
                <i class="fa-solid fa-lightbulb"></i> <span>Skills</span>
            </a>
        </li>
        {{-- Company Reviews --}}
        <li>
            <a wire:navigate href="{{ route('company-reviews.index') }}"
            class="nav-link {{ request()->routeIs('company-reviews.*') ? 'active' : '' }}">
                <i class="fa-solid fa-building-shield"></i>
                <span>Company Reviews</span>
            </a>
        </li>
        {{-- Users --}}
        <li>
            <a wire:navigate href="{{ route('users.index') }}"
               class="nav-link {{ request()->routeIs('users.*') ? 'active' : '' }}">
                <i class="fa-solid fa-user-gear"></i>
                <span>Users</span>
            </a>
        </li>

        {{-- Roles & Permissions Dropdown --}}
        <li class="nav-item">
            <a class="nav-link d-flex justify-content-between align-items-center"
               data-bs-toggle="collapse"
               href="#rolePermissionMenu"
               role="button"
               aria-expanded="{{ request()->routeIs('admin.*') ? 'true' : 'false' }}"
               aria-controls="rolePermissionMenu">
                <div>
                    <i class="fa-solid fa-shield-halved me-2"></i>
                    <span>Roles & Permissions</span>
                </div>
                <i class="fa-solid fa-angle-down small"></i>
            </a>

            <div class="collapse {{ request()->routeIs('admin.*') ? 'show' : '' }}" id="rolePermissionMenu">
                <ul class="nav flex-column ms-3 border-start ps-2">
                    <li>
                        <a wire:navigate
                           href="{{ route('admin.roles') }}"
                           class="nav-link {{ request()->routeIs('admin.roles') ? 'active' : '' }}">
                            <i class="fa-solid fa-user-shield"></i>
                            <span>Roles</span>
                        </a>
                    </li>
                    <li>
                        <a wire:navigate
                           href="{{ route('admin.permissions') }}"
                           class="nav-link {{ request()->routeIs('admin.permissions') ? 'active' : '' }}">
                            <i class="fa-solid fa-lock"></i>
                            <span>Permissions</span>
                        </a>
                    </li>
                    <li>
                        <a wire:navigate href="{{ route('admin.roles.permissions') }}"
                           class="nav-link {{ request()->routeIs('admin.roles.permissions') ? 'active' : '' }}">
                            <i class="fa-solid fa-lock"></i>
                            <span>Role Permissions</span>
                        </a>
                    </li>
                    <li>
                        <a wire:navigate
                           href="{{ route('admin.user.assign') }}"
                           class="nav-link {{ request()->routeIs('admin.user.assign') ? 'active' : '' }}">
                            <i class="fa-solid fa-users-gear"></i>
                            <span>User Roles</span>
                        </a>
                    </li>
                </ul>
            </div>
        </li>

        {{-- Employer Profile --}}
        <li>
            <a wire:navigate href="{{ route('employer.profile') }}"
               class="nav-link {{ request()->routeIs('employer.profile') ? 'active' : '' }}">
                <i class="bi bi-person-badge"></i>
                <span>Employer Profile</span>
            </a>
        </li>

        {{-- Lists --}}
        <li>
            <a wire:navigate href="{{ route('list') }}"
               class="nav-link {{ request()->routeIs('list') ? 'active' : '' }}">
                <i class="fa-solid fa-list"></i>
                <span>Lists</span>
            </a>
        </li>

        {{-- Forms --}}
        <li>
            <a wire:navigate href="{{ route('form') }}"
               class="nav-link {{ request()->routeIs('form') ? 'active' : '' }}">
                <i class="fa-solid fa-file-lines"></i>
                <span>Forms</span>
            </a>
        </li>
    </ul>
</nav>
