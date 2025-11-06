<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Forsatok') }}</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.13.1/font/bootstrap-icons.min.css">

    <!-- Bootstrap + App -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles

    <style>
        body {
            background-color: #f8f9fa;
            font-family: "Figtree", sans-serif;
        }
        .auth-image {
            background: url('{{ asset('images/job-search.jpg') }}') center/cover no-repeat;
            min-height: 500px;
        }
    </style>
</head>

<body class="d-flex justify-content-center align-items-center vh-100 bg-light">

<div class="container">
    <div class="card shadow rounded-4 overflow-hidden mx-auto" style="max-width: 900px;">
        <div class="row g-0">

            <!-- Left Image -->
            <div class="col-md-6 d-none d-md-block auth-image"></div>

            <!-- Right Content -->
            <div class="col-md-6 p-4 d-flex flex-column justify-content-center">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>

<!-- Bootstrap JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

@livewireScripts
</body>
</html>
