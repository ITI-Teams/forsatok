<!DOCTYPE html>
<html lang="en" data-bs-theme="light">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $title ?? 'Dashboard' }} - Forsatok</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.1/font/bootstrap-icons.css" rel="stylesheet">

    @livewireStyles

    <style>
        /* ==== Sidebar ==== */
        .sidebar {
            position: fixed; top: 0; left: 0;
            height: 100vh; width: 240px;
            background: #1e1f26; color: #ccc;
            transition: width 0.3s ease;
            overflow-x: hidden; z-index: 1000;
        }
        .sidebar.collapsed { width: 70px; }
        .sidebar:hover:not(.manual) { width: 240px; }
        .sidebar .nav-link {
            color: #ccc; display: flex; align-items: center;
            padding: 10px 15px; border-radius: 6px;
            transition: 0.3s; white-space: nowrap;
        }
        .sidebar .nav-link i {
            font-size: 1.3rem; margin-right: 10px;
            min-width: 30px; text-align: center;
        }
        .sidebar.collapsed .nav-link span { display: none; }
        .sidebar .nav-link.active, .sidebar .nav-link:hover {
            background-color: #0d6efd; color: #fff;
        }

        /* ==== Header ==== */
        .main-header {
            position: fixed; top: 0; left: 240px; right: 0;
            height: 60px; background: var(--bs-body-bg);
            border-bottom: 1px solid var(--bs-border-color);
            z-index: 900; transition: left 0.3s;
        }
        .sidebar.collapsed ~ .main-header { left: 70px; }

        /* ==== Content ==== */
        .content {
            margin-left: 240px; padding: 80px 20px;
            transition: margin-left 0.3s;
        }
        .sidebar.collapsed ~ .content { margin-left: 70px; }

        /* ==== Footer ==== */
        footer {
            position: fixed; bottom: 0; left: 240px; right: 0;
            background: var(--bs-body-bg);
            border-top: 1px solid var(--bs-border-color);
            text-align: center; padding: 10px; transition: left 0.3s;
        }
        .sidebar.collapsed ~ footer { left: 70px; }

        /* ==== Dark Mode ==== */
        [data-bs-theme="dark"] {
            --bs-body-bg: #121212;
            --bs-body-color: #eaeaea;
            --bs-border-color: #333;
        }
        [data-bs-theme="dark"] .sidebar { background: #181b22; }
        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0; top: 0; bottom: 0;
            width: 3px;
            background: #0d6efd;
            border-radius: 0 4px 4px 0;
            transition: all 0.3s ease;
        }
        .sidebar .nav-link { position: relative; }
    </style>
</head>
<body>

@include('livewire.layout.sidebar')
@include('livewire.layout.header')

<main class="content">
    {{ $slot }}
</main>

<footer>
    © 2025 Forsatok - All Rights Reserved
</footer>

<!-- Scripts -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
@livewireScripts

<script>
    (() => {
        const sidebar = document.getElementById("sidebar");
        const toggleSidebar = document.getElementById("toggleSidebar");
        const themeToggle = document.getElementById("themeToggle");
        const html = document.documentElement;

        if (toggleSidebar && sidebar) {
            toggleSidebar.addEventListener("click", () => {
                sidebar.classList.toggle("collapsed");
                sidebar.classList.add("manual");
                setTimeout(() => sidebar.classList.remove("manual"), 400);
            });
        }

        if (themeToggle) {
            themeToggle.addEventListener("click", () => {
                html.dataset.bsTheme = html.dataset.bsTheme === "dark" ? "light" : "dark";
                themeToggle.innerHTML = html.dataset.bsTheme === "dark"
                    ? '<i class="bi bi-sun"></i>' : '<i class="bi bi-moon"></i>';
            });
        }
    })();
</script>

</body>
</html>
