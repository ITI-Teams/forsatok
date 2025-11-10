<!DOCTYPE html>
<html lang="en" data-bs-theme="light">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - Forsatok</title>
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Bootstrap + App -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body>
    {{-- Sidebar --}}
    @include('livewire.layout.sidebar')

    {{-- Header --}}
    @include('livewire.layout.header')

    {{-- Main Content --}}
    <main class="content">
        <!-- <div class="breadcrumb-container mb-4">
        <nav aria-label="breadcrumb">
            <ol class="breadcrumb mb-0">
                <li class="breadcrumb-item"><a wire:navigate href="{{ route('dashboard') }}">Dashboard</a></li>
                @yield('breadcrumb')
            </ol>
        </nav>
    </div> -->

        <div class="">
            <livewire:breadcrumb.breadcrumb />
        </div>

        {{ $slot }}
    </main>
    {{-- Footer --}}
    <footer>© 2025 Forsatok — All Rights Reserved</footer>

    @livewireScripts
    <script>
        (() => {
            const sidebar = document.getElementById("sidebar");
            const toggleSidebar = document.getElementById("toggleSidebar");
            const themeToggle = document.getElementById("themeToggle");
            const html = document.documentElement;

            toggleSidebar?.addEventListener("click", () => {
                sidebar.classList.toggle("collapsed");
            });

            themeToggle?.addEventListener("click", () => {
                const theme = html.dataset.bsTheme === "dark" ? "light" : "dark";
                html.dataset.bsTheme = theme;
                themeToggle.innerHTML = theme === "dark"
                    ? '<i class="fa-solid fa-sun"></i>'
                    : '<i class="fa-solid fa-moon"></i>';
                localStorage.setItem("theme", theme);
            });

            const savedTheme = localStorage.getItem("theme");
            if (savedTheme) {
                html.dataset.bsTheme = savedTheme;
                themeToggle.innerHTML = savedTheme === "dark"
                    ? '<i class="fa-solid fa-sun"></i>'
                    : '<i class="fa-solid fa-moon"></i>';
            }
        })();
    </script>
    @stack('script')
</body>

</html>
