
<nav class="sidebar d-flex flex-column overflow-hidden" id="sidebar">
    <div class="text-center py-3 fs-5 fw-bold text-primary border-bottom">F</div>
    <ul class="nav flex-column mt-2">
        <li><a wire:navigate href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="fa-solid fa-gauge-high"></i><span>Dashboard</span></a></li>
        <li><a wire:navigate href="{{ route('admin') }}" class="nav-link {{ request()->routeIs('admin') ? 'active' : '' }}"><i class="fa-solid fa-user-gear"></i><span>Admins</span></a></li>
        <li><a wire:navigate href="{{ route('employer.profile') }}" class="nav-link {{ request()->routeIs('employer.profile') ? 'active' : '' }}"><i class="bi bi-person-badge"></i><span>Employer Profile</span></a></li>
        <li><a wire:navigate href="{{ route('list') }}" class="nav-link {{ request()->routeIs('list') ? 'active' : '' }}"><i class="fa-solid fa-user-gear"></i><span>Lists</span></a></li>
        <li><a wire:navigate href="{{ route('form') }}" class="nav-link {{ request()->routeIs('form') ? 'active' : '' }}"><i class="fa-solid fa-user-gear"></i><span>Forms</span></a></li>
    </ul>
</nav>
