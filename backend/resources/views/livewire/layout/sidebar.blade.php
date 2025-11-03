<nav class="sidebar d-flex flex-column" id="sidebar">
    <div class="text-center py-3 fw-bold text-primary fs-5">F</div>
    <ul class="nav flex-column px-2">
        <li><a wire:navigate href="{{ route('dashboard') }}" class="nav-link {{ request()->routeIs('dashboard') ? 'active' : '' }}"><i class="bi bi-house"></i><span>Dashboard</span></a></li>
        <li><a wire:navigate href="{{ route('admin') }}" class="nav-link {{ request()->routeIs('admin') ? 'active' : '' }}"><i class="bi bi-house"></i><span>admin</span></a></li>
{{--        <li><a wire:navigate href="{{ route('products') }}" class="nav-link {{ request()->routeIs('products') ? 'active' : '' }}"><i class="bi bi-box"></i><span>Products</span></a></li>--}}
{{--        <li><a wire:navigate href="{{ route('orders') }}" class="nav-link {{ request()->routeIs('orders') ? 'active' : '' }}"><i class="bi bi-cart"></i><span>Orders</span></a></li>--}}
{{--        <li><a wire:navigate href="{{ route('customers') }}" class="nav-link {{ request()->routeIs('customers') ? 'active' : '' }}"><i class="bi bi-person"></i><span>Customers</span></a></li>--}}
{{--        <li><a wire:navigate href="{{ route('mails') }}" class="nav-link {{ request()->routeIs('mails') ? 'active' : '' }}"><i class="bi bi-envelope"></i><span>Mails</span></a></li>--}}
{{--        <li><a wire:navigate href="{{ route('messages') }}" class="nav-link {{ request()->routeIs('messages') ? 'active' : '' }}"><i class="bi bi-chat"></i><span>Messages</span></a></li>--}}
{{--        <li><a wire:navigate href="{{ route('settings') }}" class="nav-link {{ request()->routeIs('settings') ? 'active' : '' }}"><i class="bi bi-gear"></i><span>Settings</span></a></li>--}}
    </ul>
</nav>
